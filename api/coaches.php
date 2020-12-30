<?php

declare(strict_types=1);

use Kwai\Applications\Coaches\CoachesApplication;

require '../src/vendor/autoload.php';

$app = new CoachesApplication();
$app->run();
