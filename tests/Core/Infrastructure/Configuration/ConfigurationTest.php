<?php
declare(strict_types=1);

use Dotenv\Dotenv;
use Kwai\Core\Infrastructure\Configuration\Configuration;
use Kwai\Core\Infrastructure\Configuration\DsnDatabaseConfiguration;

it('can load configuration', function () {
    $config = Configuration::createFromFile(
            __DIR__ . '/../../../../config',
            '.kwai'
    );
    expect($config->getDatabaseConfiguration())
        ->toBeObject()
    ;
    expect($config->getDatabaseConfiguration()->getDsn())
        ->toBeInstanceOf(DsnDatabaseConfiguration::class)
    ;
});
