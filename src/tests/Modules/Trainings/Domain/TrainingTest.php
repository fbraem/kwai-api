<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Date;
use Kwai\Core\Domain\ValueObjects\Event;
use Kwai\Core\Domain\ValueObjects\Gender;
use Kwai\Core\Domain\ValueObjects\Location;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Modules\Trainings\Domain\Coach;
use Kwai\Modules\Trainings\Domain\Creator;
use Kwai\Modules\Trainings\Domain\Member;
use Kwai\Modules\Trainings\Domain\Training;
use Kwai\Modules\Trainings\Domain\ValueObjects\TrainingCoach;

$creator = new Entity(
    1,
    new Creator((object)[
        'name' => 'Jigoro Kano'
    ])
);

it('can construct a training', function ($creator) {
    $training = new Training(
        (object)[
            'event' => new Event(
                Timestamp::createNow(),
                Timestamp::createNow(),
                new Location('Tokyo'),
                []
            ),
            'creator' => $creator,
            'remark' => 'Test Training'
        ]
    );
    expect($training->getRemark())
        ->toBe('Test Training')
    ;
    expect($training->getEvent()->isCancelled())
        ->toBe(false)
    ;
})->with([$creator]);

it('can appoint a coach to a training', function ($creator) {
    $training = new Training(
        (object)[
            'event' => new Event(
                Timestamp::createNow(),
                Timestamp::createNow(),
                new Location('Tokyo'),
                []
            ),
            'creator' => $creator
        ]
    );
    $member = new Entity(
        1,
        new Member((object) [
            'license' => '',
            'licenseEndDate' => Date::createFromDate(2020),
            'firstName' => '',
            'lastName' => '',
            'gender' => Gender::MALE(),
            'birthDate' => Date::createFromDate(1860, 12, 10)
        ])
    );
    $coach = new Entity(
        1,
        new Coach((object)[
            'description' => '',
            'diploma' => '',
            'active' => true,
            'creator' => $creator,
            'member' => $member
        ])
    );
    $training->appointCoach(
        new TrainingCoach($coach, false, false, false, $creator)
    );
    expect($training->getCoaches())
        ->toBeArray()
        ->toHaveCount(1)
    ;
})->with([$creator]);
