<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Applications\Domain\Application;
use Kwai\Modules\Applications\Presentation\Resources\ApplicationResource;
use Kwai\JSONAPI;

it('can serialize an application resource', function () {
    $application = new Entity(
        1,
        new Application(
            name: 'test',
            title: 'This is a test',
            description: 'This application is a test',
            shortDescription: 'A tested application',
            canHaveNews: true
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
            'name' => 'test',
            'title' => 'This is a test',
            'description' => 'This application is a test',
            'short_description' => 'A tested application',
            'news' => true,
            'pages' => false,
            'events' => false
        ])
    ;
});
