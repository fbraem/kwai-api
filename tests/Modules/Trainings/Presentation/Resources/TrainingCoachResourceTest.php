<?php
declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\JSONAPI;
use Kwai\Modules\Trainings\Domain\Coach;
use Kwai\Modules\Trainings\Domain\ValueObjects\TrainingCoach;
use Kwai\Modules\Trainings\Presentation\Resources\TrainingCoachResource;

it('can serialize a training coach to a JSON:API resource', function() {
    $trainingCoach = new TrainingCoach(
        coach: new Entity(
            1,
            new Coach(
                new Name('Jigoro', 'Kano')
            )
        ),
        creator: new Creator(
            1,
            new Name('Jigoro', 'Kano')
        ),
        head: true
    );

    $resource = new TrainingCoachResource($trainingCoach);

    try {
        $jsonapi = JSONAPI\Document::createFromObject($resource)->serialize();
    } catch (JSONAPI\Exception $e) {
        $this->fail((string) $e);
    }

    $json = json_decode($jsonapi);

    expect($json)
        ->toHaveProperty('data')
    ;
    expect($json->data)
        ->toMatchObject([
            'type' => 'training_coaches',
            'id' => '1'
        ])
        ->toHaveProperty('attributes')
    ;
    expect($json->data->attributes)
        ->toMatchObject([
            'name' => 'Jigoro Kano',
            'present' => false,
            'payed' => false,
            'head' => true,
            'remark' => null
        ])
    ;
});
