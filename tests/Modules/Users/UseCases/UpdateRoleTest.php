<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Users\Infrastructure\Repositories\RoleDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RuleDatabaseRepository;
use Kwai\Modules\Users\UseCases\UpdateRole;
use Kwai\Modules\Users\UseCases\UpdateRoleCommand;
use Tests\Context;

$context = Context::createContext();

it('can update a role', function () use ($context) {
    $command = new UpdateRoleCommand();
    $command->id = 2;
    $command->name = '';
    $command->remark = 'Updated with unit test';
    $command->rules = [ 1 ];
    try {
        $role = UpdateRole::create(
            new RoleDatabaseRepository($context->db),
            new RuleDatabaseRepository($context->db)
        )($command);
        expect($role)
            ->toBeInstanceOf(Entity::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
