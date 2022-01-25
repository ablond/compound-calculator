<?php

namespace App\Model;

class CompoundStudy
{
    public Project $project;
    public float $tokenAmount;
    public int $nodeAmount;
    public \DateTime $dateStart;
    public \DateTime $dateEnd;
}