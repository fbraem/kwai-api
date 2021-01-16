<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Users\Infrastructure\Repositories\AbilityDatabaseRepository;
use Kwai\Modules\Users\UseCases\GetAbility;
use Kwai\Modules\Users\UseCases\GetAbilityCommand;
use Tests\Context;

$context = Context::createContext();

it('can get an ability', function () use ($context) {
    $command = new GetAbilityCommand();
    $command->id = 1;

    try {
        $ability = GetAbility::create(new AbilityDatabaseRepository($context->db))($command);
        expect($ability)
            ->toBeInstanceOf(Entity::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
