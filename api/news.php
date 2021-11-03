<?php
/**
 * Entry for the news application.
 */
declare(strict_types=1);

require '../vendor/autoload.php';

use Kwai\Applications\NewsApplication;

$app = new NewsApplication();
$app->run();
