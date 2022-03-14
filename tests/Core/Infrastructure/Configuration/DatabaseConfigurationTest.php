<?php

declare(strict_types=1);

use Kwai\Core\Infrastructure\Configuration\DatabaseConfiguration;
use Kwai\Core\Infrastructure\Configuration\DsnDatabaseConfiguration;

it('can configure a database', function () {
    $config = new DatabaseConfiguration(
        new DsnDatabaseConfiguration(
            'mysql:host=localhost;dbname=testdb;port=3306;charset=utf8mb4'
        ),
        'test',
        'test1234'
    );
    expect($config->getDsn()->getDatabaseName())
        ->toBe('testdb');
    expect($config->getUser())->toBe('test');
    expect($config->getPassword())->toBe('test1234');
});
