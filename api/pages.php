<?php
/**
 * Entry for the pages application.
 */
declare(strict_types=1);

require '../src/vendor/autoload.php';

use Kwai\Applications\PagesApplication;

$app = new PagesApplication();
$app->run();
