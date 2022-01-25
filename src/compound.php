<?php

namespace App;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;

require_once __DIR__.'/../vendor/autoload.php';

$startDate = new \DateTime();

$projects = [
    'Atlas' => [
        'rewardPerNode'        => [
            [
                'date' => $startDate,
                'reward' => 0.1,
            ],
        ],
        'costPerNode'          => 10,
        'tokenValue'           => 350,
        'monthlyFeePerNode'    => 25,
        'claimTax'             => 0,
        'currentTokenOwned'    => 5,
        'currentNodeOwned'     => 3,
    ],
    'Thor' => [
        'rewardPerNode'        => [
            [
                'date' => new \DateTime('2022-02-15'),
                'reward' => 0.1,
            ],[
                'date' => $startDate,
                'reward' => 0.33,
            ],
        ],
        'costPerNode'          => 12.5,
        'tokenValue'           => 200,
        'monthlyFeePerNode'    => 0,
        'claimTax'             => 0.2,
        'currentTokenOwned'    => 8,
        'currentNodeOwned'     => 2,
    ],
    'Power' => [
        'rewardPerNode'        => [
            [
                'date' => $startDate,
                'reward' => 0.7,
            ],
        ],
        'costPerNode'          => 50,
        'tokenValue'           => 20,
        'monthlyFeePerNode'    => 0,
        'claimTax'             => 0.1,
        'currentTokenOwned'    => 15,
        'currentNodeOwned'     => 1,
    ]
];

$compoundLimitDate = new \DateTime('2022-07-04');

echo sprintf("------ Compound Stop Date : %s \n", $compoundLimitDate->format('Y-m-d'));

$output = new ConsoleOutput();
$table = new Table($output);

$table->setHeaders([
    'Project',
    'Node number',
    'Remaining Token Number',
    'Monthly Passive income',
]);


foreach ($projects as $projectLabel => $project) {
    list($nodeNumber, $token) = calculateNodeNumberAfterCompoundPeriod($project, $startDate, $compoundLimitDate);

    $tokenPerMonth = getRewardPerNode($project, $compoundLimitDate) * 30 * $nodeNumber;
    $claimTax = $tokenPerMonth * $project['claimTax'];
    $tokenPerMonth -= $claimTax;
    $tokenPerMonthValue = $tokenPerMonth * $project['tokenValue'];
    $monthlyFees = $project['monthlyFeePerNode'] * $nodeNumber;
    $monthlyPassiveIncome = $tokenPerMonthValue - $monthlyFees;

    $table->addRow([
        $projectLabel,
        $nodeNumber,
        number_format($token, 2, ',', ' '),
        '$' . number_format($monthlyPassiveIncome, 0, ',', ' ')
    ]);
}

$table->render();

function calculateNodeNumberAfterCompoundPeriod($project, $startDate, $compoundLimitDate)
{
    $nodeNumber = $project['currentNodeOwned'];
    $token = $project['currentTokenOwned'];
    $date = clone $startDate;

    for ($i = 0; $i < $compoundLimitDate->diff($startDate)->days; $i++) {
        $rewardPerNode = getRewardPerNode($project, $date);
        $token += $rewardPerNode * $nodeNumber;

        $date->modify('+1 day');

        while  ($token >= $project['costPerNode']) {
            ++$nodeNumber;
            $token -= $project['costPerNode'];
        }
    }

    return [$nodeNumber, $token];
}

function getRewardPerNode($project, $date)
{
    foreach ($project['rewardPerNode'] as $rewardPerNode) {
        if ($rewardPerNode['date'] <= $date) {
            return $rewardPerNode['reward'];
        }
    }
}

