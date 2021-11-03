<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Users\Infrastructure\Repositories\AbilityDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RuleDatabaseRepository;
use Kwai\Modules\Users\UseCases\CreateAbility;
use Kwai\Modules\Users\UseCases\CreateAbilityCommand;
use Tests\Context;

$context = Context::createContext();

it('can create an ability', function () use ($context) {
    $ruleRepo = new RuleDatabaseRepository($context->db);
    try {
        $rules = $ruleRepo->getAll(
            $ruleRepo->createQuery()->filterBySubject('trainings')
        );
    } catch (Exception $e) {
        $this->fail((string) $e);
    }

    $command = new CreateAbilityCommand();
    $command->name = 'Coach';
    $command->remark = 'Rules for a coach';
    $command->rules = $rules->keys()->toArray();
    try {
        $ability = CreateAbility::create(
            new AbilityDatabaseRepository($context->db),
            $ruleRepo
        )($command);
    } catch (Exception $e) {
        $this->fail((string) $e);
    }

    expect($ability)
        ->toBeInstanceOf(Entity::class)
    ;
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
