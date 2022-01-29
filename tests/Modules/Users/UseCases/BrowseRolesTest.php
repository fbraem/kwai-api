<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Modules\Users\Infrastructure\Repositories\RoleDatabaseRepository;
use Kwai\Modules\Users\UseCases\BrowseRoles;
use Kwai\Modules\Users\UseCases\BrowseRolesCommand;
use Tests\Context;

$context = Context::createContext();

it('can browse roles', function () use ($context) {
    $command = new BrowseRolesCommand();
    try {
        [ $count, $roles ] = BrowseRoles::create(
            new RoleDatabaseRepository($context->db)
        )($command);
        expect($count)
            ->toBeInt()
        ;
        expect($roles)
            ->toBeInstanceOf(Collection::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
