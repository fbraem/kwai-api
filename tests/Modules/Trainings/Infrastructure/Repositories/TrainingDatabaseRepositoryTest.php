<?php
declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Core\Domain\ValueObjects\Event;
use Kwai\Core\Domain\ValueObjects\Locale;
use Kwai\Core\Domain\ValueObjects\Location;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Modules\Trainings\Domain\Coach;
use Kwai\Modules\Trainings\Domain\CoachEntity;
use Kwai\Modules\Trainings\Domain\Exceptions\TrainingNotFoundException;
use Kwai\Modules\Trainings\Domain\Team;
use Kwai\Modules\Trainings\Domain\TeamEntity;
use Kwai\Modules\Trainings\Domain\Training;
use Kwai\Modules\Trainings\Domain\TrainingEntity;
use Kwai\Modules\Trainings\Infrastructure\Repositories\TrainingDatabaseRepository;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can get a training', function () {
    $repo = new TrainingDatabaseRepository($this->db);
    try {
        $training = $repo->getById(1);
        expect($training)
            ->toBeInstanceOf(TrainingEntity::class)
            ->and($training->getEvent())
            ->toBeInstanceOf(Event::class)
        ;
        /*
        expect($training->getCoaches()->count())
            ->toBeGreaterThan(0)
        ;
        */
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can count all trainings', function () {
    $repo = new TrainingDatabaseRepository($this->db);
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
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('should throw a not found exception', function () {
    $repo = new TrainingDatabaseRepository($this->db);
    /** @noinspection PhpUnhandledExceptionInspection */
    $repo->getById(1000);
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
    ->throws(TrainingNotFoundException::class)
;

it('can filter trainings on year and month', function () {
    $repo = new TrainingDatabaseRepository($this->db);
    try {
        $query = $repo->createQuery();
        $query->filterYearMonth(2020, 12);
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
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can filter trainings for a coach', function () {
    $repo = new TrainingDatabaseRepository($this->db);
    try {
        $query = $repo->createQuery();
        $query->filterCoach(
            new CoachEntity(
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
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can filter trainings for a team', function () {
    $repo = new TrainingDatabaseRepository($this->db);
    try {
        $query = $repo->createQuery();
        $query->filterTeam(
            new TeamEntity(
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
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can create a training', function () {
    $repo = new TrainingDatabaseRepository($this->db);

    $training = new Training(
        event: new Event(
            startDate: Timestamp::createFromString('2020-12-13 20:00:00'),
            endDate: Timestamp::createFromString('2020-12-13 21:00:00'),
            timezone: 'Europe/Brussels',
            location: new Location('Sports hall of the club')
        ),
        text: new Collection([
            new Text(
                locale: Locale::EN,
                format: DocumentFormat::MARKDOWN,
                title: 'Training for competitors',
                author: new Creator(1, new Name('Jigoro', 'Kano')),
                summary: 'This is a training for competitive members only',
                content: null
            )
        ]),
        remark: 'This training is created from a unit test',
    );
    try {
        $entity = $repo->create($training);
        expect($entity)
            ->toBeInstanceOf(TrainingEntity::class)
            ->and($entity->domain())
            ->toBeInstanceOf(Training::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can get a training with presences', function () {
    $repo = new TrainingDatabaseRepository($this->db);
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
        ->toBeInstanceOf(TrainingEntity::class)
        ->and($trainings->first()->getPresences())
        ->toBeInstanceOf(Collection::class)
    ;
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
