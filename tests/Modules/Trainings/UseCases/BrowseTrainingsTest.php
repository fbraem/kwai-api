<?php
declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Modules\Trainings\Domain\Exceptions\CoachNotFoundException;
use Kwai\Modules\Trainings\Infrastructure\Repositories\CoachDatabaseRepository;
use Kwai\Modules\Trainings\Infrastructure\Repositories\DefinitionDatabaseRepository;
use Kwai\Modules\Trainings\Infrastructure\Repositories\TrainingDatabaseRepository;
use Kwai\Modules\Trainings\UseCases\BrowseTrainings;
use Kwai\Modules\Trainings\UseCases\BrowseTrainingsCommand;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can browse trainings', function () {
    $repo = new TrainingDatabaseRepository($this->db);
    $coachRepo = new CoachDatabaseRepository($this->db);
    $defRepo = new DefinitionDatabaseRepository($this->db);
    $command = new BrowseTrainingsCommand();
    try {
        [$count, $trainings] = BrowseTrainings::create(
            $repo,
            $coachRepo,
            $defRepo
        )($command);
        expect($count)
            ->toBeGreaterThan(0)
        ;
        expect($trainings)
            ->toBeInstanceOf(Collection::class)
            ->and($trainings->count())
            ->toBeGreaterThan(0)
        ;
        expect($trainings->first())
            ->toBeInstanceOf(Entity::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can browse trainings for a given year and month', function () {
    $repo = new TrainingDatabaseRepository($this->db);
    $coachRepo = new CoachDatabaseRepository($this->db);
    $defRepo = new DefinitionDatabaseRepository($this->db);

    $command = new BrowseTrainingsCommand();
    $command->year = 2020;
    $command->month = 9;
    try {
        [$count, $trainings] = BrowseTrainings::create(
            $repo,
            $coachRepo,
            $defRepo
        )($command);
        expect($count)
            ->toBeGreaterThan(0)
        ;
        expect($trainings)
            ->toBeInstanceOf(Collection::class)
            ->and($trainings->count())
            ->toBeGreaterThan(0)
        ;
        expect($trainings->first())
            ->toBeInstanceOf(Entity::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can browse trainings for a coach', function () {
    $repo = new TrainingDatabaseRepository($this->db);
    $coachRepo = new CoachDatabaseRepository($this->db);
    $defRepo = new DefinitionDatabaseRepository($this->db);

    $command = new BrowseTrainingsCommand();
    $command->coach = 1;
    try {
        [$count, $trainings] = BrowseTrainings::create(
            $repo,
            $coachRepo,
            $defRepo
        )($command);
        expect($count)
            ->toBeGreaterThan(0)
        ;
        expect($trainings)
            ->toBeInstanceOf(Collection::class)
            ->and($trainings->count())
            ->toBeGreaterThan(0)
        ;
        expect($trainings->first())
            ->toBeInstanceOf(Entity::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('should throw an exception when coach does not exist', function () {
    $repo = new TrainingDatabaseRepository($this->db);
    $coachRepo = new CoachDatabaseRepository($this->db);
    $defRepo = new DefinitionDatabaseRepository($this->db);

    $command = new BrowseTrainingsCommand();
    $command->coach = 1000;

    /** @noinspection PhpUnhandledExceptionInspection */
    BrowseTrainings::create($repo, $coachRepo, $defRepo)($command);
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
    ->expectException(CoachNotFoundException::class)
;
