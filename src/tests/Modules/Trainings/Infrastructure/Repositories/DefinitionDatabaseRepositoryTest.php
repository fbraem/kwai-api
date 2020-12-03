<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Exceptions\TrainingDefinitionNotFoundException;
use Kwai\Modules\Trainings\Domain\Definition;
use Kwai\Modules\Trainings\Infrastructure\Repositories\CoachDatabaseRepository;
use Kwai\Modules\Trainings\Infrastructure\Repositories\DefinitionDatabaseRepository;
use Tests\Context;

$context = Context::createContext();

it('can get a training definition', function () use ($context) {
    $repo = new DefinitionDatabaseRepository($context->db);
    try {
        $definition = $repo->getById(1);
        expect($definition)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($definition->domain())
            ->toBeInstanceOf(Definition::class)
        ;
    } catch (TrainingDefinitionNotFoundException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can get all training definitions', function () use ($context) {
    $repo = new DefinitionDatabaseRepository($context->db);
    try {
        $query = $repo->createQuery();
        $definitions = $query->execute();
        expect($definitions)
            ->toBeInstanceOf(Collection::class)
        ;
        $definition = $definitions->first();
        expect($definition)
            ->toBeInstanceOf(Entity::class)
            ->and($definition->domain())
            ->toBeInstanceOf(Definition::class)
        ;
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (QueryException $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
