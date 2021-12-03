<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Modules\News\Domain\Application;
use Kwai\Modules\News\Presentation\Resources\ApplicationResource;
use Kwai\JSONAPI;

it('can serialize an application resource', function () {
    $application = new Entity(
        1,
        new Application(
            name: 'Test',
            title: 'Test title',
        )
    );

    $resource = new ApplicationResource($application);

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
            'type' => 'applications',
            'id' => '1'
        ])
        ->toHaveProperty('attributes')
    ;
    expect($json->data->attributes)
        ->toMatchObject([
            'name' => 'Test',
            'title' => 'Test title'
        ])
    ;
});
