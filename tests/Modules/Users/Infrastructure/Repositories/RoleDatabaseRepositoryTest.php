<?php
declare(strict_types=1);

namespace Tests\Modules\Users\Infrastructure\Repositories;

use Exception;
use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Role;
use Kwai\Modules\Users\Infrastructure\Repositories\RoleDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RuleDatabaseRepository;
use Tests\Context;

$context = Context::createContext();

it('can create a role', function () use ($context) {
    $ruleRepo = new RuleDatabaseRepository($context->db);
    try {
        $rule = $ruleRepo->getByIds([1])->first();
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }

    $repo = new RoleDatabaseRepository($context->db);
    try {
        $role = $repo->create(new Role(
            name: 'Test',
            remark: 'Test Role',
            rules: new Collection([$rule])
        ));
        expect($role)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($role->domain())
            ->toBeInstanceOf(Role::class)
        ;
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
    return $role;
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can update a role', function ($role) use ($context) {
    if ($role == null) {
        return;
    }
    $repo = new RoleDatabaseRepository($context->db);
    try {
        $updatedRole = new Role(
            name: $role->getName(),
            remark: 'Updated with a test',
            traceableTime: $role->getTraceableTime()->markUpdated()
        );
        $repo->update(
            new Entity(
                $role->id(),
                $updatedRole
            )
        );
        $this->assertTrue(true);
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
})
    ->depends('it can create a role')
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can get an role', function ($role) use ($context) {
    if ($role == null) {
        return;
    }

    $repo = new RoleDatabaseRepository($context->db);
    try {
        $role = $repo->getById($role->id());
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
    expect($role)
        ->toBeInstanceOf(Entity::class)
    ;
    expect($role->domain())
        ->toBeInstanceOf(Role::class)
    ;
})
    ->depends('it can create a role')
    ->skip(!Context::hasDatabase(), 'No database available')
;
