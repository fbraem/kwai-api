<?php
declare(strict_types=1);

use function Kwai\Core\Infrastructure\createApplication;

require '../src/vendor/autoload.php';

$application = createApplication();
$application->run();
