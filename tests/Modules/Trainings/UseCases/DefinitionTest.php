<?php
declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\Weekday;
use Kwai\Modules\Trainings\Domain\Definition;
use Kwai\Modules\Trainings\Infrastructure\Repositories\DefinitionDatabaseRepository;
use Kwai\Modules\Trainings\Infrastructure\Repositories\SeasonDatabaseRepository;
use Kwai\Modules\Trainings\Infrastructure\Repositories\TeamDatabaseRepository;
use Kwai\Modules\Trainings\UseCases\BrowseDefinitions;
use Kwai\Modules\Trainings\UseCases\BrowseDefinitionsCommand;
use Kwai\Modules\Trainings\UseCases\CreateDefinition;
use Kwai\Modules\Trainings\UseCases\CreateDefinitionCommand;
use Kwai\Modules\Trainings\UseCases\DeleteDefinition;
use Kwai\Modules\Trainings\UseCases\DeleteDefinitionCommand;
use Kwai\Modules\Trainings\UseCases\GetDefinition;
use Kwai\Modules\Trainings\UseCases\GetDefinitionCommand;
use Kwai\Modules\Trainings\UseCases\UpdateDefinition;
use Kwai\Modules\Trainings\UseCases\UpdateDefinitionCommand;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can create a definition', function() {
    $command = new CreateDefinitionCommand();
    $command->name = 'Training Competitors';
    $command->description = 'Each monday for competitors';
    $command->start_time = '19:00';
    $command->end_time = '20:00';
    $command->time_zone = 'Europe/Brussels';
    $command->weekday = Weekday::MONDAY;
    $command->remark = 'Definition created while unit testing';
    $command->location = 'Sports hall of the city';

    $creator = new Creator(1, new Name('Jigoro', 'Kano'));

    $definitionRepo = new DefinitionDatabaseRepository($this->db);
    $teamRepo = new TeamDatabaseRepository($this->db);
    $seasonRepo = new SeasonDatabaseRepository($this->db);

    try {
        $entity = CreateDefinition::create($definitionRepo, $teamRepo, $seasonRepo)
            ($command, $creator);
        expect($entity)
            ->toBeInstanceOf(Entity::class)
            ->and($entity->domain())
            ->toBeInstanceOf(Definition::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }

    return $entity->id();
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can get a definition', function($id) {
    $repo = new DefinitionDatabaseRepository($this->db);

    $command = new GetDefinitionCommand();
    $command->id = $id;

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
    ->depends('it can create a definition')
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can browse definitions', function () {
    $repo = new DefinitionDatabaseRepository($this->db);
    $command = new BrowseDefinitionsCommand();
    try {
        [$count, $definitions] = BrowseDefinitions::create($repo)($command);
        expect($count)
            ->toBeGreaterThan(0)
        ;
        expect($definitions)
            ->toBeInstanceOf(Collection::class)
            ->and($definitions->count())
            ->toBeGreaterThan(0)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can update a definition', function ($id) {
    $command = new UpdateDefinitionCommand();
    $command->id = $id;
    $command->name = 'Training Competitors';
    $command->description = 'Each monday for competitors';
    $command->start_time = '20:00';
    $command->end_time = '21:00';
    $command->time_zone = 'Europe/Brussels';
    $command->weekday = Weekday::MONDAY;
    $command->active = true;
    $command->remark = 'Definition updated while unit testing';
    $command->location = 'Sports hall of the city';

    $definitionRepo = new DefinitionDatabaseRepository($this->db);
    $teamRepo = new TeamDatabaseRepository($this->db);
    $seasonRepo = new SeasonDatabaseRepository($this->db);

    $creator = new Creator(1, new Name('Jigoro', 'Kano'));

    try {
        $entity = UpdateDefinition::create($definitionRepo, $teamRepo, $seasonRepo)
            ($command, $creator);
        expect($entity)
            ->toBeInstanceOf(Entity::class)
            ->and($entity->domain())
            ->toBeInstanceOf(Definition::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->depends('it can create a definition')
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can remove a definition', function ($id) {
    $command = new DeleteDefinitionCommand();
    $command->id = $id;

    $definitionRepo = new DefinitionDatabaseRepository($this->db);
    try {
        DeleteDefinition::create($definitionRepo)($command);
        $this->expectNotToPerformAssertions();
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->depends('it can create a definition')
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
