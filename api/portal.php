<?php
/**
 * @package Kwai
 * @subpackage api
 */
declare(strict_types=1);

require '../vendor/autoload.php';

use Kwai\Applications\PortalApplication;

$app = new PortalApplication();
$app->run();
