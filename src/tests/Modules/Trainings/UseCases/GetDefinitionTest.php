<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Trainings\Domain\Definition;
use Kwai\Modules\Trainings\Infrastructure\Repositories\DefinitionDatabaseRepository;
use Kwai\Modules\Trainings\UseCases\GetDefinition;
use Kwai\Modules\Trainings\UseCases\GetDefinitionCommand;
use Tests\Context;

$context = Context::createContext();

it('can get a definition', function () use ($context) {
    $repo = new DefinitionDatabaseRepository($context->db);

    $command = new GetDefinitionCommand();
    $command->id = 1;

    try {
        $definition = GetDefinition::create($repo)($command);
        expect($definition)
            ->toBeInstanceOf(Entity::class)
            ->and($definition->domain())
            ->toBeInstanceOf(Definition::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
