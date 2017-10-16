<?php

use Core\Application;

define('CLUBMAN_ABSPATH', str_replace('\\', '/', dirname(__FILE__)) . '/');

require './vendor/autoload.php';

$application = \Core\Clubman::getApplication();
$application->run();
