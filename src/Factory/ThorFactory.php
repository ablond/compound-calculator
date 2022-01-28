<?php

namespace App\Factory;

use App\Model\DailyReward;
use App\Model\Node;
use App\Model\Project;
use App\Model\Token;

class ThorFactory
{

    static public function build(): Project
    {
        $project = new Project();
        $project->name = 'Thor Financial';

        $token = new Token();
        $token->name = 'THOR';
        $token->value = 150;

        $node = new Node();
        $node->name = 'Thor';
        $node->claimTax = 0.15;
        $node->costInToken = 12.5;
        $node->monthlyFee = 0;

        $dailyReward = new DailyReward();
        $dailyReward->token = $token;
        $dailyReward->amount = 0.33;
        $dailyReward->dateStart = new \DateTime();
        $dailyReward->dateStop = new \DateTime('2022-02-15');
        $node->token = $token;
        $node->dailyReward[] = $dailyReward;

        $dailyReward = new DailyReward();
        $dailyReward->token = $token;
        $dailyReward->amount = 0.125;
        $dailyReward->dateStart = new \DateTime('2022-02-06');
        $node->token = $token;
        $node->dailyReward[] = $dailyReward;

        $project->node = $node;

        return $project;
    }
}
