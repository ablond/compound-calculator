<?php

namespace App\Model;

class Node
{
    public string $name;
    public Token $token;
    public float $costInToken;
    /** @var DailyReward[] */
    public array $dailyReward;
    public float $claimTax;
    public float $monthlyFee;
}