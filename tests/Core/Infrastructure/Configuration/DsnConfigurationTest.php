<?php
declare(strict_types=1);

use Kwai\Core\Infrastructure\Configuration\DsnConfiguration;

it('can create a DSN', function () {
    $config = DsnConfiguration::create(
        scheme: 'smtp',
        user: 'test',
        pwd: 'test1234',
        host: 'localhost.be',
        port: 2525
    );

    expect((string) $config)
        ->toEqual('smtp://test:test1234@localhost.be:2525')
    ;
});
