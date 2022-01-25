<?php

namespace App\Model;

class DailyReward
{
    public float $amount;
    public Token $token;
    public \DateTime $dateStart;
    public ?\DateTime $dateStop = null;
}