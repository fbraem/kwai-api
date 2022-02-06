<?php
/**
 * api entry for users
 */
declare(strict_types=1);

require '../autoload.php';

use Kwai\Applications\Users\UsersApplication;

$app = new UsersApplication();
$app->run();
