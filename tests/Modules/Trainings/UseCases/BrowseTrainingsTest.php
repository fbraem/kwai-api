<?php
declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Modules\Trainings\Domain\Coach;
use Kwai\Modules\Trainings\Domain\CoachEntity;
use Kwai\Modules\Trainings\Domain\Exceptions\CoachNotFoundException;
use Kwai\Modules\Trainings\Domain\TrainingEntity;
use Kwai\Modules\Trainings\Infrastructure\Repositories\CoachDatabaseRepository;
use Kwai\Modules\Trainings\Infrastructure\Repositories\DefinitionDatabaseRepository;
use Kwai\Modules\Trainings\Infrastructure\Repositories\TrainingDatabaseRepository;
use Kwai\Modules\Trainings\Repositories\CoachRepository;
use Kwai\Modules\Trainings\UseCases\BrowseTrainings;
use Kwai\Modules\Trainings\UseCases\BrowseTrainingsCommand;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(function() {
    $this->withDatabase();
    $this->coachRepo = new class($this->db) extends CoachDatabaseRepository implements CoachRepository {
        public function getById(int ...$ids): Collection
        {
            $coaches = new Collection();
            foreach($ids as $id) {
                $coaches->put(
                    $id,
                    new CoachEntity(
                        $id,
                        new Coach(
                            name: new Name(
                                firstname: 'Jigoro',
                                lastname: 'Kano'
                            )
                        )
                    )
                );
            }
            return $coaches;
        }
    };
});

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
            ->toBeInstanceOf(TrainingEntity::class)
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
    $command->month = 12;
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
            ->toBeInstanceOf(TrainingEntity::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can browse trainings for a coach', function () {
    $repo = new TrainingDatabaseRepository($this->db);
    $defRepo = new DefinitionDatabaseRepository($this->db);

    $command = new BrowseTrainingsCommand();
    $command->coach = 2;
    try {
        [$count, $trainings] = BrowseTrainings::create(
            $repo,
            $this->coachRepo,
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
            ->toBeInstanceOf(TrainingEntity::class)
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
