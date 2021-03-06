<?php
/**
 * @package Kwai
 * @subpackage Facebook
 */
declare(strict_types=1);

require __DIR__ . '/src/vendor/autoload.php';

use Kwai\Applications\FacebookApplication;

$app = new FacebookApplication();
$app->run();
