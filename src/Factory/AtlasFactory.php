<?php

namespace App\Factory;

use App\Model\DailyReward;
use App\Model\Node;
use App\Model\Project;
use App\Model\Token;

class AtlasFactory
{
    static public function build(): Project
    {
        $project = new Project();
        $project->name = 'Atlas';

        $token = new Token();
        $token->name = 'ATLAS';
        $token->value = 350;

        $node = new Node();
        $node->name = 'Atlas';
        $node->claimTax = 0;
        $node->costInToken = 10;
        $node->monthlyFee = 25;

        $dailyReward = new DailyReward();
        $dailyReward->token = $token;
        $dailyReward->amount = 0.1;
        $dailyReward->dateStart = new \DateTime();
        $node->token = $token;
        $node->dailyReward[] = $dailyReward;

        $project->node = $node;

        return $project;
    }
}