<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Users\Infrastructure\Repositories\AbilityDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RuleDatabaseRepository;
use Kwai\Modules\Users\UseCases\UpdateAbility;
use Kwai\Modules\Users\UseCases\UpdateAbilityCommand;
use Tests\Context;

$context = Context::createContext();

it('can update an ability', function () use ($context) {
    $command = new UpdateAbilityCommand();
    $command->id = 2;
    $command->name = '';
    $command->remark = 'Updated with unit test';
    $command->rules = [ 1 ];
    try {
        $ability = UpdateAbility::create(
            new AbilityDatabaseRepository($context->db),
            new RuleDatabaseRepository($context->db)
        )($command);
        expect($ability)
            ->toBeInstanceOf(Entity::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
