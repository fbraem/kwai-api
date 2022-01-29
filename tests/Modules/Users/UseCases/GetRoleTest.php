<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Users\Infrastructure\Repositories\RoleDatabaseRepository;
use Kwai\Modules\Users\UseCases\GetRole;
use Kwai\Modules\Users\UseCases\GetRoleCommand;
use Tests\Context;

$context = Context::createContext();

it('can get a role', function () use ($context) {
    $command = new GetRoleCommand();
    $command->id = 1;

    try {
        $role = GetRole::create(new RoleDatabaseRepository($context->db))($command);
        expect($role)
            ->toBeInstanceOf(Entity::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
