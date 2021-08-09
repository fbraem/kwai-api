<?php

declare(strict_types=1);

use Kwai\Modules\News\Presentation\REST\StorySchema;
use Nette\Schema\ValidationException;

it('throws an exception on empty data', function () {
    $schema = new StorySchema(true);
    $schema->normalize([]);
})->expectException(ValidationException::class);

it('throws an exception on invalid type', function () {
    $schema = new StorySchema(true);
    $schema->normalize([
        'data' => [
            'type' => 'wrong'
        ]
    ]);
})->expectException(ValidationException::class);

it('can normalize data', function () {
    $schema = new StorySchema(true);
    try {
        $result = $schema->normalize([
            'data' => [
                'type' => 'stories',
                'attributes' => [
                    'publish_date' => '2021-01-02 10:00',
                    'timezone' => 'Europe/Brussels',
                    'promotion' => 1,
                    'contents' => [
                        [
                            'title' => 'Test',
                            'summary' => 'Test',
                            'content' => 'This is a test'
                        ]
                    ]
                ]
            ]
        ]);
        expect($result)->toBeObject();
        expect($result->data)->toBeObject();
        expect($result->data->type)->toBe('stories');
        expect($result->data->attributes->publish_date)->toBe('2021-01-02 10:00');
        expect($result->data->attributes->contents)->toBeArray();
        expect($result->data->attributes->contents[0]->title)->toBe('Test');
    } catch (ValidationException $ve) {
        $this->fail(strval($ve));
    }
});

it('can detect an invalid publish date', function () {
    $schema = new StorySchema();
    $result = $schema->normalize([
        'data' => [
            'type' => 'stories',
            'attributes' => [
                'publish_date' => '20-01-02 10:00',
                'timezone' => 'Europe/Brussels',
                'contents' => [
                    [
                        'title' => 'Test',
                        'summary' => 'Test'
                    ]
                ]
            ]
        ]
    ]);
})->expectException(ValidationException::class);

it('can detect a missing publish date', function () {
    $schema = new StorySchema();
    $result = $schema->normalize([
        'data' => [
            'type' => 'stories',
            'attributes' => [
                'timezone' => 'Europe/Brussels',
                'contents' => [
                    [
                        'title' => 'Test',
                        'summary' => 'Test'
                    ]
                ]
            ]
        ]
    ]);
})->expectException(ValidationException::class);

it('can detect an invalid end date', function () {
    $schema = new StorySchema();
    $result = $schema->normalize([
        'data' => [
            'type' => 'stories',
            'attributes' => [
                'publish_date' => '2022-01-02 10:00',
                'end_date' => '22-01-02 10:00',
                'timezone' => 'Europe/Brussels',
                'contents' => [
                    [
                        'title' => 'Test',
                        'summary' => 'Test'
                    ]
                ]
            ]
        ]
    ]);
})->expectException(ValidationException::class);

it('can detect an invalid promotion value', function () {
    $schema = new StorySchema();
    $result = $schema->normalize([
        'data' => [
            'type' => 'stories',
            'attributes' => [
                'publish_date' => '2022-01-02 10:00',
                'timezone' => 'Europe/Brussels',
                'promotion' => '1',
                'contents' => [
                    [
                        'title' => 'Test',
                        'summary' => 'Test'
                    ]
                ]
            ]
        ]
    ]);
})->expectException(ValidationException::class);

it('can detect an invalid promotion end date', function () {
    $schema = new StorySchema();
    $schema->normalize([
        'data' => [
            'type' => 'stories',
            'attributes' => [
                'publish_date' => '2022-01-02 10:00',
                'promotion_end_date' => '22-01-02 10:00',
                'timezone' => 'Europe/Brussels',
                'contents' => [
                    [
                        'title' => 'Test',
                        'summary' => 'Test'
                    ]
                ]
            ]
        ]
    ]);
})->expectException(ValidationException::class);

it('can normalize data with an id', function () {
    $schema = new StorySchema();
    try {
        $result = $schema->normalize([
            'data' => [
                'type' => 'stories',
                'id' => '1',
                'attributes' => [
                    'publish_date' => '2021-01-02 10:00',
                    'timezone' => 'Europe/Brussels',
                    'contents' => [
                        [
                            'title' => 'Test',
                            'summary' => 'Test',
                            'content' => 'This is a test'
                        ]
                    ]
                ]
            ]
        ]);
        expect($result)->toBeObject();
        expect($result->data->id)->toBeString();
    } catch (ValidationException $ve) {
        $this->fail(strval($ve));
    }
});

it('can normalize with an application relationship', function () {
    $json = <<<JSON
        {
            "data": {
                "type": "stories",
                "attributes": { 
                    "enabled": false,
                    "promotion": 0,
                    "timezone":"Europe/Brussels",
                    "publish_date": "2021-08-08 14:46:00",
                    "remark":null,
                    "contents":[
                        { 
                            "locale":"nl",
                            "format":"md",
                            "title":"test",
                            "summary":"test",
                            "content":"Dit is een langere tekst..."
                            }
                        ]
                },
                "relationships": {
                    "application": {
                        "data": {
                            "type": "applications",
                            "id":"1"
                        }
                    }
                },
                "id": "162"
            }
        }
JSON;
    $schema = new StorySchema();
    try {
        $result = $schema->normalize(json_decode($json));
        expect($result)->toBeObject();
    } catch (ValidationException $ve) {
        $this->fail(strval($ve));
    }
});
