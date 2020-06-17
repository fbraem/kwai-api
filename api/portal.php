<?php
/**
 * @package Kwai
 * @subpackage api
 */
declare(strict_types=1);

require '../src/vendor/autoload.php';

use Kwai\Applications\Portal\PortalApplication;

$app = new PortalApplication();
$app->run();
