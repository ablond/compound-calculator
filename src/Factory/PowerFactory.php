<?php

namespace App\Factory;

use App\Model\DailyReward;
use App\Model\Node;
use App\Model\Project;
use App\Model\Token;

class PowerFactory
{
    static public function build(): Project
    {
        $project = new Project();
        $project->name = 'Power';

        $token = new Token();
        $token->name = 'POWER';
        $token->value = 20;

        $node = new Node();
        $node->name = 'Superhuman';
        $node->claimTax = 0.1;
        $node->costInToken = 50;
        $node->monthlyFee = 0;

        $dailyReward = new DailyReward();
        $dailyReward->token = $token;
        $dailyReward->amount = 0.7;
        $dailyReward->dateStart = new \DateTime();
        $node->token = $token;
        $node->dailyReward[] = $dailyReward;

        $project->node = $node;

        return $project;
    }
}