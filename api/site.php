<?php
/**
 * @package Kwai
 * @subpackage api
 */
declare(strict_types=1);

require '../src/vendor/autoload.php';

use Kwai\Applications\Site\SiteApplication;

$app = new SiteApplication();
$app->run();
