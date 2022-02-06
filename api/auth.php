<?php
/**
 * api entry for auth
 */
declare(strict_types=1);

require '../autoload.php';

use Kwai\Applications\Auth\AuthApplication;

$app = new AuthApplication();
$app->run();
