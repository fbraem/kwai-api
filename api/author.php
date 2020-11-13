<?php
/**
 * Entry for the author application.
 */
declare(strict_types=1);

require '../src/vendor/autoload.php';

use Kwai\Applications\Author\AuthorApplication;

$app = new AuthorApplication();
$app->run();
