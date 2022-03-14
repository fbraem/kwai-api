<?php

declare(strict_types=1);

use Kwai\Core\Infrastructure\Configuration\LoggerConfiguration;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

it('can configure a logger', function () {
    $config = LoggerConfiguration::createFromVariables([
        'LOG_FILE' => '',
        'LOG_LEVEL' => LogLevel::ERROR
    ]);
    $logger = $config->createLogger('test');
    expect($logger)->toBeInstanceOf(LoggerInterface::class);
});
