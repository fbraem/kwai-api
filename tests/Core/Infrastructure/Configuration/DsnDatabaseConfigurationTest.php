<?php

declare(strict_types=1);

use Kwai\Core\Infrastructure\Configuration\DsnDatabaseConfiguration;

it('can read a database DSN', function() {
    $dsn = 'mysql:host=localhost;dbname=test;port=3306;charset=utf8mb4';
    $config = new DsnDatabaseConfiguration(
        'mysql:host=localhost;dbname=test;port=3306;charset=utf8mb4'
    );
    expect($config->getDriver())->toEqual('mysql');
    expect((string) $config)->toEqual($dsn);
    expect($config->getHost())->toEqual('localhost');
    expect($config->getDatabaseName())->toEqual('test');
});
