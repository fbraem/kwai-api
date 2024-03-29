<?php
declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Modules\Trainings\Domain\Coach;
use Kwai\Modules\Trainings\Domain\CoachEntity;
use Kwai\Modules\Trainings\Domain\TrainingEntity;
use Kwai\Modules\Trainings\Infrastructure\Repositories\CoachDatabaseRepository;
use Kwai\Modules\Trainings\Infrastructure\Repositories\DefinitionDatabaseRepository;
use Kwai\Modules\Trainings\Infrastructure\Repositories\TeamDatabaseRepository;
use Kwai\Modules\Trainings\Infrastructure\Repositories\TrainingDatabaseRepository;
use Kwai\Modules\Trainings\Repositories\CoachRepository;
use Kwai\Modules\Trainings\UseCases\CreateTraining;
use Kwai\Modules\Trainings\UseCases\CreateTrainingCommand;
use Kwai\Modules\Trainings\UseCases\GetTraining;
use Kwai\Modules\Trainings\UseCases\GetTrainingCommand;
use Kwai\Modules\Trainings\UseCases\UpdateTraining;
use Kwai\Modules\Trainings\UseCases\UpdateTrainingCommand;
use Tests\DatabaseTrait;

$coachRepo = null;

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

it('can create a training', function () {
    $command = new CreateTrainingCommand();

    $command->start_date = '2020-12-16 20:00:00';
    $command->end_date = '2020-12-16 21:00:00';
    $command->timezone = 'Europe/Brussels';
    $command->location = 'Sports hall';
    $command->remark = 'Training created with unittest';
    $command->contents[] = (object)[
        'locale' => 'nl',
        'format' => 'md',
        'title' => 'Training for competitors',
        'summary' => 'Only for competitive players'
    ];

    try {
        $entity = CreateTraining::create(
            new TrainingDatabaseRepository($this->db),
            new DefinitionDatabaseRepository($this->db),
            new TeamDatabaseRepository($this->db),
            $this->coachRepo
        )($command, new Creator(1, new Name('Jigoro', 'Kano')));
        expect($entity)
            ->toBeInstanceOf(TrainingEntity::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
    return $entity->id();
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can update a training', function(int $id) {
    $command = new UpdateTrainingCommand();
    $command->id = $id;
    $command->remark = 'Updated with unit test';
    $command->start_date = '2020-12-16 20:00:00';
    $command->end_date = '2020-12-16 21:00:00';
    $command->timezone = 'Europe/Brussels';
    $command->location = 'Sports hall';
    $command->contents[] = (object) [
        'locale' => 'nl',
        'format' => 'md',
        'title' => 'Competitors',
        'summary' => 'Training for the competitors',
        'content' => ''
    ];
    $command->teams = [ 2 ];
    $command->coaches = [
        (object) [
            'id' => 2,
            'head' => false,
            'present' => false,
            'payed' => false,
            'remark' => ''
        ],
        (object) [
            'id' => 3,
            'head' => false,
            'present' => false,
            'payed' => false,
            'remark' => ''
        ],
    ];

    try {
        $entity = UpdateTraining::create(
            new TrainingDatabaseRepository($this->db),
            new DefinitionDatabaseRepository($this->db),
            new TeamDatabaseRepository($this->db),
            $this->coachRepo
        )($command, new Creator(1, new Name('Jigoro', 'Kano')));
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
    expect($entity)
        ->toBeInstanceOf(TrainingEntity::class)
    ;
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
    ->depends('it can create a training')
;

it('can get a training', function (int $id) {
    $command = new GetTrainingCommand();
    $command->id = $id;

    try {
        $training = GetTraining::create(new TrainingDatabaseRepository(
            $this->db
        ))($command);
        expect($training)
            ->toBeInstanceOf(TrainingEntity::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
    ->depends('it can create a training')
;
