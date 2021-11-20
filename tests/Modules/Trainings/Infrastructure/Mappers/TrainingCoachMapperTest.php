<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Modules\Trainings\Domain\Coach;
use Kwai\Modules\Trainings\Domain\ValueObjects\TrainingCoach;
use Kwai\Modules\Trainings\Infrastructure\Mappers\TrainingCoachMapper;

it('can map a record to TrainingCoach', function () {
    $record = collect([
       'coach' => collect([
           'id' => '1',
           'firstname' => 'Jigoro',
           'lastname' => 'Kano'
       ]),
       'creator' => collect([
           'id' => '1',
           'first_name' => 'Jigoro',
           'last_name' => 'Kano'
       ]),
       'remark' => 'Unit test training coach',
       'coach_type' => '1',
       'present' => '1',
       'payed' => '0',
       'created_at' => '2020-12-14 21:09:00'
    ]);

    $trainingCoach = TrainingCoachMapper::toDomain($record);

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
        coach: new Entity(
            1,
            new Coach(new Name('Jigoro', 'Kano'))
        ),
        creator: new Creator(
            1,
            new Name('Jigoro', 'Kano')
        ),
        payed: false,
        head: true,
        present: false,
        traceableTime: new TraceableTime()
    );

    $record = TrainingCoachMapper::toPersistence($trainingCoach);

    expect($record)
       ->toBeInstanceOf(Collection::class)
       ->and($record->get('coach_type'))
       ->toBe(1)
       ->and($record->get('present'))
       ->toBe(false)
       ->and($record->get('user_id'))
       ->toBe(1)
   ;
});
