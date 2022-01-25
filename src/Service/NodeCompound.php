<?php

namespace App\Service;

use App\Factory\AtlasFactory;
use App\Factory\PowerFactory;
use App\Factory\ThorFactory;
use App\Model\CompoundStudy;
use App\Model\Node;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

class NodeCompound
{
    protected ConsoleOutput $output;
    protected Table $table;

    private function init()
    {
        $this->output = new ConsoleOutput();
        $this->table = new Table($this->output);

        $this->table->setHeaders([
            'Project',
            'Node number',
            'Remaining Token',
            'Monthly Token Claimable',
            'Token Value',
            'Monthly Passive income',
        ]);
    }

    public function process()
    {
        $this->init();

        $maxCompoundDate = new \DateTime('2022-07-08');

        $thorStudy = new CompoundStudy();
        $thorStudy->dateStart = new \DateTime('2022-01-30');
        $thorStudy->dateEnd = $maxCompoundDate;
        $thorStudy->project = ThorFactory::build();
        $thorStudy->nodeAmount = 3;
        $thorStudy->tokenAmount = 0;

        $this->processCompoundStudy($thorStudy);

        $atlasStudy = new CompoundStudy();
        $atlasStudy->dateStart = new \DateTime('2022-01-7');
        $atlasStudy->dateEnd = $maxCompoundDate;
        $atlasStudy->project = AtlasFactory::build();
        $atlasStudy->nodeAmount = 3;
        $atlasStudy->tokenAmount = 0;

        $this->processCompoundStudy($atlasStudy);

        $powerStudy = new CompoundStudy();
        $powerStudy->dateStart = new \DateTime('2022-01-15');
        $powerStudy->dateEnd = $maxCompoundDate;
        $powerStudy->project = PowerFactory::build();
        $powerStudy->nodeAmount = 1;
        $powerStudy->tokenAmount = 0;

        $this->processCompoundStudy($powerStudy);

        $this->table->render();
    }

    private function processCompoundStudy(CompoundStudy $compoundStudy)
    {
        $this->calculateNodeNumberAfterCompoundPeriod($compoundStudy);

        $tokenPerMonth = $this->getRewardPerNode($compoundStudy->project->node, $compoundStudy->dateEnd) * 30 * $compoundStudy->nodeAmount;
        $claimTax = $tokenPerMonth * $compoundStudy->project->node->claimTax;
        $tokenPerMonth -= $claimTax;
        $tokenPerMonthValue = $tokenPerMonth * $compoundStudy->project->node->token->value;
        $monthlyFees = $compoundStudy->project->node->monthlyFee * $compoundStudy->nodeAmount;
        $monthlyPassiveIncome = $tokenPerMonthValue - $monthlyFees;

        $this->table->addRow([
            $compoundStudy->project->name,
            $compoundStudy->nodeAmount,
            $compoundStudy->tokenAmount,
            number_format($tokenPerMonth, 2, ',', ' '),
            '$' . number_format($compoundStudy->project->node->token->value, 2, ',', ' '),
            '$' . number_format($monthlyPassiveIncome, 0, ',', ' '),
        ]);
    }

    private function calculateNodeNumberAfterCompoundPeriod(CompoundStudy $compoundStudy)
    {
        $date = clone $compoundStudy->dateStart;

        for ($i = 0; $i < $compoundStudy->dateEnd->diff($compoundStudy->dateStart)->days; $i++) {
            $rewardPerNode = $this->getRewardPerNode($compoundStudy->project->node, $date);
            $compoundStudy->tokenAmount += $rewardPerNode * $compoundStudy->nodeAmount;

            $date->modify('+1 day');

            while  ($compoundStudy->tokenAmount >= $compoundStudy->project->node->costInToken) {
                ++$compoundStudy->nodeAmount;
                $compoundStudy->tokenAmount -= $compoundStudy->project->node->costInToken;
            }
        }
    }

    private function getRewardPerNode(Node $node, \DateTime $date): float
    {
        foreach ($node->dailyReward as $dailyReward) {
            if (null !== $dailyReward->dateStop) {
                if ($dailyReward->dateStart <= $date && $date <= $dailyReward->dateStop) {
                    return $dailyReward->amount;
                }
            } else {
                if ($dailyReward->dateStart <= $date) {
                    return $dailyReward->amount;
                }
            }
        }

        return .0;
    }
}