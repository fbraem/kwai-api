<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Modules\Trainings\Domain\Exceptions\CoachNotFoundException;
use Kwai\Modules\Trainings\Infrastructure\Repositories\CoachDatabaseRepository;
use Kwai\Modules\Trainings\Infrastructure\Repositories\TrainingDatabaseRepository;
use Kwai\Modules\Trainings\UseCases\BrowseTrainings;
use Kwai\Modules\Trainings\UseCases\BrowseTrainingsCommand;
use Tests\Context;

$context = Context::createContext();

it('can browse trainings', function () use ($context) {
    $repo = new TrainingDatabaseRepository($context->db);
    $coachRepo = new CoachDatabaseRepository($context->db);
    $command = new BrowseTrainingsCommand();
    try {
        [$count, $trainings] = BrowseTrainings::create($repo, $coachRepo)($command);
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
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can browse trainings for a given year/month', function () use ($context) {
    $repo = new TrainingDatabaseRepository($context->db);
    $coachRepo = new CoachDatabaseRepository($context->db);
    $command = new BrowseTrainingsCommand();
    $command->year = 2020;
    $command->month = 9;
    try {
        [$count, $trainings] = BrowseTrainings::create($repo, $coachRepo)($command);
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
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can browse trainings for a coach', function () use ($context) {
    $repo = new TrainingDatabaseRepository($context->db);
    $coachRepo = new CoachDatabaseRepository($context->db);
    $command = new BrowseTrainingsCommand();
    $command->coach = 1;
    try {
        [$count, $trainings] = BrowseTrainings::create($repo, $coachRepo)($command);
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
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('should throw an exception when coach does not exist', function () use ($context) {
    $repo = new TrainingDatabaseRepository($context->db);
    $coachRepo = new CoachDatabaseRepository($context->db);
    $command = new BrowseTrainingsCommand();
    $command->coach = 1000;

    /** @noinspection PhpUnhandledExceptionInspection */
    BrowseTrainings::create($repo, $coachRepo)($command);
})
    ->skip(!Context::hasDatabase(), 'No database available')
    ->expectException(CoachNotFoundException::class)
;
