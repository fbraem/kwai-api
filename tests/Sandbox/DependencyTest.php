<?php

declare(strict_types=1);

use Kwai\Core\Infrastructure\Dependencies\Dependency;


class TestDependency implements Dependency
{
    public function __construct()
    {
    }

    public function create(): string
    {
        return 'TEST';
    }
}

it('can depend', function () {
    $db = depends('test.dependency', TestDependency::class);
    expect($db)->toBeString()->toEqual('TEST');
});
