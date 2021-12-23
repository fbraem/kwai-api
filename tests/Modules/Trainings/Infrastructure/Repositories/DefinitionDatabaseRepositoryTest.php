<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\Time;
use Kwai\Core\Domain\ValueObjects\TimePeriod;
use Kwai\Core\Domain\ValueObjects\Weekday;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Exceptions\DefinitionNotFoundException;
use Kwai\Modules\Trainings\Domain\Definition;
use Kwai\Modules\Trainings\Infrastructure\Repositories\DefinitionDatabaseRepository;
use Tests\Context;

$context = Context::createContext();

it('can create a definition', function () use ($context) {
    $repo = new DefinitionDatabaseRepository($context->db);
    $definition = new Definition(
        name: 'Test',
        description: 'Created while running unittest',
        weekday: new Weekday(1),
        period: new TimePeriod(
            new Time(19, 0, 'Europe/Brussels'),
            new Time(20, 0, 'Europe/Brussels')
        ),
        creator: new Creator(
            1,
            new Name('Jigoro', 'Kano')
        )
    );
    try {
        $entity = $repo->create($definition);
        expect($entity)
            ->toBeInstanceOf(Entity::class)
            ->and($entity->id())
            ->toBeInt()
        ;
        return $entity->id();
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can update a definition', function ($id) use ($context) {
    $repo = new DefinitionDatabaseRepository($context->db);

    try {
        $definition = $repo->getById($id);
    } catch (Exception $e) {
        $this->fail((string) $e);
    }

    /* @var $definition Definition */
    $traceableTime = $definition->getTraceableTime()->markUpdated();

    $newDefinition = new Entity($id, new Definition(
        name: $definition->getName(),
        description: 'Updated while running unittest',
        weekday: $definition->getWeekDay(),
        period: $definition->getPeriod(),
        creator: $definition->getCreator(),
        traceableTime: $traceableTime
    ));
    try {
        $repo->update($newDefinition);
        $this->expectNotToPerformAssertions();
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
    ->depends('it can create a definition')
;

it('can delete a definition', function ($id) use ($context) {
    $repo = new DefinitionDatabaseRepository($context->db);

    try {
        $definition = $repo->getById($id);
    } catch (Exception $e) {
        $this->fail((string) $e);
    }

    try {
        $repo->remove($definition);
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
    $this->expectNotToPerformAssertions();
})
    ->skip(!Context::hasDatabase(), 'No database available')
    ->depends('it can create a definition')
;

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
    } catch (DefinitionNotFoundException $e) {
        $this->fail((string) $e);
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can get all training definitions', function () use ($context) {
    $repo = new DefinitionDatabaseRepository($context->db);
    try {
        $query = $repo->createQuery();
        $definitions = $repo->getAll($query);
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
        $this->fail((string) $e);
    } catch (QueryException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
