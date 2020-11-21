<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Date;
use Kwai\Core\Domain\ValueObjects\Event;
use Kwai\Core\Domain\ValueObjects\Gender;
use Kwai\Core\Domain\ValueObjects\Location;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Modules\Trainings\Domain\Coach;
use Kwai\Modules\Trainings\Domain\Member;
use Kwai\Modules\Trainings\Domain\Training;
use Kwai\Modules\Trainings\Domain\ValueObjects\Creator;
use Kwai\Modules\Trainings\Domain\ValueObjects\Presence;
use Kwai\Modules\Trainings\Domain\ValueObjects\TrainingCoach;

$creator = new Creator(
    1,
    new Name('Jigoro', 'Kano')
);

it('can construct a training', function () use ($creator) {
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
});

it('can appoint/release a coach to a training', function () use ($creator) {
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
            'name' => new Name(),
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
    $training->releaseCoach($coach);
    expect($training->getCoaches())
        ->toBeArray()
        ->toHaveCount(0)
    ;
});

it('can manager a presence', function () use ($creator) {
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
            'name' => new Name(),
            'gender' => Gender::MALE(),
            'birthDate' => Date::createFromDate(1860, 12, 10)
        ])
    );
    $training->registerPresence(new Presence(
        $member,
        $creator
    ));
    expect($training->getPresences())
        ->toBeArray()
        ->toHaveCount(1);
    $training->unregisterPresence($member);
    expect($training->getPresences())
        ->toBeArray()
        ->toHaveCount(0);
});
