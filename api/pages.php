<?php
/**
 * Entry for the pages application.
 */
declare(strict_types=1);

require '../src/vendor/autoload.php';

use Kwai\Applications\Pages\PagesApplication;

$app = new PagesApplication();
$app->run();
