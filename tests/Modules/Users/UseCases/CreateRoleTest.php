<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Users\Infrastructure\Repositories\RoleDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RuleDatabaseRepository;
use Kwai\Modules\Users\UseCases\CreateRole;
use Kwai\Modules\Users\UseCases\CreateRoleCommand;
use Tests\Context;

$context = Context::createContext();

it('can create a role', function () use ($context) {
    $ruleRepo = new RuleDatabaseRepository($context->db);
    try {
        $rules = $ruleRepo->getAll(
            $ruleRepo->createQuery()->filterBySubject('trainings')
        );
    } catch (Exception $e) {
        $this->fail((string) $e);
    }

    $command = new CreateRoleCommand();
    $command->name = 'Coach';
    $command->remark = 'Rules for a coach';
    $command->rules = $rules->keys()->toArray();
    try {
        $role = CreateRole::create(
            new RoleDatabaseRepository($context->db),
            $ruleRepo
        )($command);
    } catch (Exception $e) {
        $this->fail((string) $e);
    }

    expect($role)
        ->toBeInstanceOf(Entity::class)
    ;
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
