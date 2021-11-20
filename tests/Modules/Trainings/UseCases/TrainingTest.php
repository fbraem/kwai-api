<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Modules\Trainings\Infrastructure\Repositories\CoachDatabaseRepository;
use Kwai\Modules\Trainings\Infrastructure\Repositories\DefinitionDatabaseRepository;
use Kwai\Modules\Trainings\Infrastructure\Repositories\TeamDatabaseRepository;
use Kwai\Modules\Trainings\Infrastructure\Repositories\TrainingDatabaseRepository;
use Kwai\Modules\Trainings\UseCases\CreateTraining;
use Kwai\Modules\Trainings\UseCases\CreateTrainingCommand;
use Kwai\Modules\Trainings\UseCases\GetTraining;
use Kwai\Modules\Trainings\UseCases\GetTrainingCommand;
use Kwai\Modules\Trainings\UseCases\UpdateTraining;
use Kwai\Modules\Trainings\UseCases\UpdateTrainingCommand;
use Tests\Context;

$context = Context::createContext();

it('can create a training', function () use ($context) {
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
            new TrainingDatabaseRepository($context->db),
            new DefinitionDatabaseRepository($context->db),
            new TeamDatabaseRepository($context->db),
            new CoachDatabaseRepository($context->db)
        )($command, new Creator(1, new Name('Jigoro', 'Kano')));
        expect($entity)
            ->toBeInstanceOf(Entity::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
    return $entity->id();
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can update a training', function(int $id) use ($context) {
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
            new TrainingDatabaseRepository($context->db),
            new DefinitionDatabaseRepository($context->db),
            new TeamDatabaseRepository($context->db),
            new CoachDatabaseRepository($context->db)
        )($command, new Creator(1, new Name('Jigoro', 'Kano')));
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
    expect($entity)
        ->toBeInstanceOf(Entity::class)
    ;
})
    ->skip(!Context::hasDatabase(), 'No database available')
    ->depends('it can create a training')
;

it('can get a training', function (int $id) use ($context) {
    $command = new GetTrainingCommand();
    $command->id = $id;

    try {
        $training = GetTraining::create(new TrainingDatabaseRepository(
            $context->db
        ))($command);
        expect($training)
            ->toBeInstanceOf(Entity::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
    ->depends('it can create a training')
;
