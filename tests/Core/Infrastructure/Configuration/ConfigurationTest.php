<?php
declare(strict_types=1);

use Kwai\Core\Infrastructure\Configuration\Configuration;
use Kwai\Core\Infrastructure\Configuration\DsnDatabaseConfiguration;

it('can load configuration', function () {
    $config = new Configuration();
    $config->load();
    expect($config->getDatabaseConfiguration())
        ->toBeObject()
    ;
    expect($config->getDatabaseConfiguration()->getDsn())
        ->toBeInstanceOf(DsnDatabaseConfiguration::class)
    ;
});
