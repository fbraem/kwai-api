<?php
declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Core\Domain\ValueObjects\Event;
use Kwai\Core\Domain\ValueObjects\Locale;
use Kwai\Core\Domain\ValueObjects\Location;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Core\Domain\ValueObjects\Timestamp;
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
        /* @var Training $training */
        expect($training->getEvent())
            ->toBeInstanceOf(Event::class)
        ;
        expect($training->getCoaches()->count())
            ->toBeGreaterThan(0)
        ;
    } catch (Exception $e) {
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
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('should throw a not found exception', function () use ($context) {
    $repo = new TrainingDatabaseRepository($context->db);
    /** @noinspection PhpUnhandledExceptionInspection */
    $repo->getById(1000);
})
    ->skip(!Context::hasDatabase(), 'No database available')
    ->throws(TrainingNotFoundException::class)
;

it('can filter trainings on year and month', function () use ($context) {
    $repo = new TrainingDatabaseRepository($context->db);
    try {
        $query = $repo->createQuery();
        $query->filterYearMonth(2019, 8);
        $trainings = $repo->getAll($query);
        expect($trainings)
            ->toBeInstanceOf(Collection::class)
            ->and($trainings->count())
            ->toBeGreaterThan(0)
        ;
    } catch (Exception $e) {
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
        $trainings = $repo->getAll($query);
        expect($trainings)
            ->toBeInstanceOf(Collection::class)
            ->and($trainings->count())
            ->toBeGreaterThan(0)
        ;
    } catch (Exception $e) {
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
        $trainings = $repo->getAll($query);
        expect($trainings)
            ->toBeInstanceOf(Collection::class)
            ->and($trainings->count())
            ->toBeGreaterThan(0)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can create a training', function () use ($context) {
    $repo = new TrainingDatabaseRepository($context->db);

    $training = new Training(
        event: new Event(
            startDate: Timestamp::createFromString('2020-12-13 20:00:00'),
            endDate: Timestamp::createFromString('2020-12-13 21:00:00'),
            location: new Location('Sports hall of the club'),
            timezone: 'Europe/Brussels'
        ),
        text: new Collection([
            new Text(
                locale: new Locale('en'),
                format: new DocumentFormat('md'),
                title: 'Training for competitors',
                summary: 'This is a training for competitive members only',
                content: null,
                author: new Creator(1, new Name('Jigoro', 'Kano'))
            )
        ]),
        remark: 'This training is created from a unit test',
    );
    try {
        $entity = $repo->create($training);
        expect($entity)
            ->toBeInstanceOf(Entity::class)
            ->and($entity->domain())
            ->toBeInstanceOf(Training::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can get a training with presences', function () use ($context) {
    $repo = new TrainingDatabaseRepository($context->db);
    $query = $repo
        ->createQuery()
        ->filterId(1)
        ->withPresences()
    ;
    try {
        $trainings = $repo->getAll($query);
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
    expect($trainings)
        ->toBeInstanceOf(Collection::class)
        ->and($trainings->count())
        ->toBe(1)
        ->and($trainings->first())
        ->toBeInstanceOf(Entity::class)
        ->and($trainings->first()->getPresences())
        ->toBeInstanceOf(Collection::class)
    ;
})
    ->skip(!Context::hasDatabase(), 'No database available')
;
