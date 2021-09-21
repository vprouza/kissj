<?php

declare(strict_types=1);

use kissj\Application\ApplicationGetter;

require __DIR__ . '/../vendor/autoload.php';

session_start();

$app = (new ApplicationGetter())->getApp();

// Run app
$app->run();
