<?php

namespace App;

use App\Service\NodeCompound;

require_once __DIR__.'/../vendor/autoload.php';

$nodeCompound = new NodeCompound();
$nodeCompound->process();