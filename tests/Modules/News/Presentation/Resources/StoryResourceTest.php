<?php

declare(strict_types=1);

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\LocalTimestamp;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Infrastructure\Converter\ConverterFactory;
use Kwai\Modules\Applications\Domain\Application;
use Kwai\Modules\News\Domain\Story;
use Kwai\Modules\News\Presentation\Resources\StoryResource;
use Kwai\JSONAPI;

it('can serialize an story resource', function () {
    $now = Timestamp::createNow();
    $application = new Entity(
        1,
        new Application(
            'Test',
            'Test title',
            'This is a test',
            'Test'
        )
    );
    $contents = collect();
    $story = new Entity(1, new Story(
        new LocalTimestamp($now, 'Europe/Brussels'),
        $application,
        $contents
    ));

    $resource = new StoryResource($story, new ConverterFactory());
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
            'type' => 'stories',
            'id' => '1'
        ])
        ->toHaveProperties([
            'attributes',
            'relationships'
        ])
    ;
    expect($json->data->attributes)
        ->toMatchObject([
            'timezone' => 'Europe/Brussels',
            'enabled' => false,
            'publish_date' => (string) $now,
            'contents' => []
        ])
    ;
    expect($json->data->relationships)
        ->toBeObject()
        ->toHaveProperty('application')
    ;
    expect($json->data->relationships->application)
        ->toHaveProperty('data')
    ;
    expect($json->data->relationships->application->data)
        ->toMatchObject([
            'type' => 'applications',
            'id' => '1'
        ])
    ;
    expect($json)
        ->toHaveProperty('included')
    ;
    expect($json->included)
        ->toBeArray()
        ->not->toBeEmpty()
    ;

    // No need to test the application resource,
    // that's done in ApplicationResourceTest
});
