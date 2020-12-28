<?php

declare(strict_types=1);

use Kwai\Applications\Coach\CoachApplication;

require '../src/vendor/autoload.php';

$app = new CoachApplication();
$app->run();
