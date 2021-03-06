<?php
/**
 * api entry for auth
 */
declare(strict_types=1);

require '../src/vendor/autoload.php';

use Kwai\Applications\AuthApplication;

$app = new AuthApplication();
$app->run();
