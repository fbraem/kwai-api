<?php

declare(strict_types=1);

use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Modules\Trainings\Domain\Coach;
use Kwai\Modules\Trainings\Domain\CoachEntity;
use Kwai\Modules\Trainings\Domain\ValueObjects\TrainingCoach;
use Kwai\Modules\Trainings\Infrastructure\Mappers\TrainingCoachDTO;
use Kwai\Modules\Trainings\Infrastructure\MembersTable;
use Kwai\Modules\Trainings\Infrastructure\PersonsTable;
use Kwai\Modules\Trainings\Infrastructure\TrainingCoachesTable;
use Kwai\Modules\Trainings\Infrastructure\UsersTable;

it('can map a record to TrainingCoach', function () {
    $trainingCoachRecord = collect([
        'tc_training_id' => 1,
        'tc_coach_id' => 1,
        'tc_user_id' => 1,
        'tc_remark' => 'Unit test training coach',
        'tc_coach_type' => 1,
        'tc_present' => 1,
        'tc_payed' => 0,
        'tc_created_at' => '2020-12-14 21:09:00'
    ]);
    $memberRecord = collect([
        'm_id' => 1
    ]);
    $personRecord = collect([
        'p_id' => 1,
        'p_firstname' => 'Jigoro',
        'p_lastname' => 'Kano'
    ]);
    $userRecord = collect([
        'u_id' => 1,
        'u_first_name' => 'Jigoro',
        'u_last_name' => 'Kano'
    ]);

    $trainingCoach = (new TrainingCoachDTO(
        TrainingCoachesTable::createFromRow($trainingCoachRecord, 'tc'),
        MembersTable::createFromRow($memberRecord, 'm'),
        PersonsTable::createFromRow($personRecord, 'p'),
        UsersTable::createFromRow($userRecord, 'u')
    ))->create();

    expect($trainingCoach)
       ->toBeInstanceOf(TrainingCoach::class)
       ->and($trainingCoach->isHead())
       ->toBe(true)
       ->and($trainingCoach->isPayed())
       ->toBe(false)
       ->and($trainingCoach->isPresent())
       ->toBe(true)
       ->and($trainingCoach->getCreator())
       ->toBeInstanceOf(Creator::class)
    ;
});

it('can map a TrainingCoach to a record', function () {
    $trainingCoach = new TrainingCoach(
        coach: new CoachEntity(
            1,
            new Coach(new Name('Jigoro', 'Kano'))
        ),
        creator: new Creator(
            1,
            new Name('Jigoro', 'Kano')
        ),
        head: true,
        present: false,
        payed: false,
        traceableTime: new TraceableTime()
    );

    $record = (new TrainingCoachDTO())->persist($trainingCoach)->trainingCoach;

    expect($record)
       ->toBeInstanceOf(TrainingCoachesTable::class)
       ->and($record->coach_type)
       ->toBe(1)
       ->and($record->present)
       ->toBe(0)
       ->and($record->user_id)
       ->toBe(1)
   ;
});
