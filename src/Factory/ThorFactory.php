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
        $project->name = 'ThorFactory Financial';

        $token = new Token();
        $token->name = 'THOR';
        $token->value = 220;

//        $node = new Node();
//        $node->name = 'Heimdall';
//        $node->claimTax = 0.1;
//        $node->costInToken = 1.25;
//        $node->monthlyFee = 0;
//
//        $dailyReward = new DailyReward();
//        $dailyReward->token = $token;
//        $dailyReward->amount = 0.02125;
//        $dailyReward->dateStart = new \DateTime();
//
//        $node->token = $token;
//        $node->dailyRewardInToken[] = $dailyReward;
//        $project->nodes[] = $node;

//        $node = new Node();
//        $node->name = 'Freya';
//        $node->claimTax = 0.15;
//        $node->costInToken = 6.25;
//        $node->monthlyFee = 0;
//
//        $dailyReward = new DailyReward();
//        $dailyReward->token = $token;
//        $dailyReward->amount = 0.13125;
//        $dailyReward->dateStart = new \DateTime();
//        $node->token = $token;
//        $node->dailyRewardInToken[] = $dailyReward;
//
//        $project->nodes[] = $node;

        $node = new Node();
        $node->name = 'Thor';
        $node->claimTax = 0.2;
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
        $dailyReward->amount = 0.1;
        $dailyReward->dateStart = new \DateTime('2022-02-15');
        $node->token = $token;
        $node->dailyReward[] = $dailyReward;

        $project->node = $node;

//        $node = new Node();
//        $node->name = 'Odin';
//        $node->claimTax = 0.25;
//        $node->costInToken = 78.125;
//        $node->monthlyFee = 0;
//
//        $dailyReward = new DailyReward();
//        $dailyReward->token = $token;
//        $dailyReward->amount = 2.7335;
//        $dailyReward->dateStart = new \DateTime();
//        $node->token = $token;
//        $node->dailyRewardInToken[] = $dailyReward;
//
//        $project->nodes[] = $node;

        return $project;
    }
}