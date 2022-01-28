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
    }

    private function initTable()
    {
        $this->table = new Table($this->output);

        $this->table->setHeaders([
            'Date',
            'Project',
            'Node number',
            'Remaining Token',
            'Mth Token Claimable',
            'Token Value',
            'Mth Psv Income',
            'Mth Psv Income (Net)',
        ]);
    }

    public function process()
    {
        $this->init();

        $maxCompoundDates = [
            new \DateTime('2022-03-01'),
            new \DateTime('2022-04-01'),
            new \DateTime('2022-05-01'),
            new \DateTime('2022-06-01'),
            new \DateTime('2022-07-01'),
            new \DateTime('2022-08-01'),
        ];

        $this->runCompoundStudies(ThorFactory::class, 2, 10.905992, $maxCompoundDates);
        $this->runCompoundStudies(AtlasFactory::class, 3, 6.139, $maxCompoundDates);
        $this->runCompoundStudies(PowerFactory::class, 1, 9.546072, $maxCompoundDates);
    }

    private function runCompoundStudies($factoryClass, int $nodeAmount, float $tokenAmount, array $maxCompoundDates): void
    {
        $this->initTable();

        foreach ($maxCompoundDates as $maxCompoundDate) {
            $powerStudy = new CompoundStudy();
            $powerStudy->dateStart = new \DateTime('2022-01-28');
            $powerStudy->dateEnd = $maxCompoundDate;
            $powerStudy->project = $factoryClass::build();
            $powerStudy->nodeAmount = $nodeAmount;
            $powerStudy->tokenAmount = $tokenAmount;

            $this->processCompoundStudy($powerStudy);
        }

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
        $monthlyPassiveIncomeWithoutTax = $monthlyPassiveIncome - ($monthlyPassiveIncome * 0.3);

        $this->table->addRow([
            $compoundStudy->dateEnd->format('d/m/Y'),
            $compoundStudy->project->name,
            $compoundStudy->nodeAmount,
            number_format($compoundStudy->tokenAmount, 2, ',', ' '),
            number_format($tokenPerMonth, 2, ',', ' '),
            '$' . number_format($compoundStudy->project->node->token->value, 2, ',', ' '),
            '$' . number_format($monthlyPassiveIncome, 0, ',', ' '),
            '$' . number_format($monthlyPassiveIncomeWithoutTax, 0, ',', ' '),
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
