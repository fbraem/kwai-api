<?php
declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Event;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Coach;
use Kwai\Modules\Trainings\Domain\Exceptions\TrainingNotFoundException;
use Kwai\Modules\Trainings\Domain\Team;
use Kwai\Modules\Trainings\Domain\Training;
use Kwai\Modules\Trainings\Infrastructure\Repositories\TrainingDatabaseRepository;
use Tests\Context;

$context = Context::createContext();

it('can get a training', function () use ($context) {
    $repo = new TrainingDatabaseRepository($context->db);
    try {
        $training = $repo->getById(1);
        expect($training)
            ->toBeInstanceOf(Entity::class)
        ;
        expect($training->domain())
            ->toBeInstanceOf(Training::class)
        ;
        /** @noinspection PhpUndefinedMethodInspection */
        expect($training->getEvent())
            ->toBeInstanceOf(Event::class)
        ;
        /** @noinspection PhpUndefinedMethodInspection */
        expect($training->getCoaches()->count())
            ->toBeGreaterThan(0)
        ;
    } catch (TrainingNotFoundException $e) {
        $this->fail((string) $e);
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can count all trainings', function () use ($context) {
    $repo = new TrainingDatabaseRepository($context->db);
    try {
        $query = $repo->createQuery();
        $count = $query->count();
        expect($count)
            ->toBeGreaterThan(0)
        ;
    } catch (QueryException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('should throw a not found exception', function () use ($context) {
    $repo = new TrainingDatabaseRepository($context->db);
    try {
        /** @noinspection PhpUnhandledExceptionInspection */
        $repo->getById(1000);
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
    ->throws(TrainingNotFoundException::class)
;

it('can filter trainings on year/month', function () use ($context) {
    $repo = new TrainingDatabaseRepository($context->db);
    try {
        $query = $repo->createQuery();
        $query->filterYearMonth(2019, 8);
        $trainings = $repo->execute($query);
        expect($trainings)
            ->toBeInstanceOf(Collection::class)
            ->and($trainings->count())
            ->toBeGreaterThan(0)
        ;
    } catch (QueryException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can filter trainings for a coach', function () use ($context) {
    $repo = new TrainingDatabaseRepository($context->db);
    try {
        $query = $repo->createQuery();
        $query->filterCoach(
            new Entity(
                1,
                new Coach(
                    name: new Name('Jigoro', 'Kano')
                )
            )
        );
        $trainings = $repo->execute($query);
        expect($trainings)
            ->toBeInstanceOf(Collection::class)
            ->and($trainings->count())
            ->toBeGreaterThan(0)
        ;
    } catch (QueryException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can filter trainings for a team', function () use ($context) {
    $repo = new TrainingDatabaseRepository($context->db);
    try {
        $query = $repo->createQuery();
        $query->filterTeam(
            new Entity(
                1,
                new Team('U11')
            )
        );
        $trainings = $repo->execute($query);
        expect($trainings)
            ->toBeInstanceOf(Collection::class)
            ->and($trainings->count())
            ->toBeGreaterThan(0)
        ;
    } catch (QueryException $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
