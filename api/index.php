<?php

use Kwai\Core\Infrastructure\Application;

require '../src/vendor/autoload.php';

$application = \Kwai\Core\Infrastructure\Clubman::getApplication();
$application->run();
