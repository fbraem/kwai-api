<?php

declare(strict_types=1);

const JSON_WRONG_TYPE = <<<JSON
    {
        "data": {
            "type": "wrong"
        }
    }
JSON;

const JSON_WRONG_PUBLISH_DATE = <<<JSON
    {
        "data": {
            "type": "stories",
            "attributes": {
                "publish_date":  "20-01-02 10:00:00",
                "timezone": "Europe/Brussels",
                "contents": [
                    {
                        "title": "Test",
                        "summary": "Test"
                    }
                ]
            }
        }
    }
JSON;

const JSON_MISSING_PUBLISH_DATE = <<<JSON
    {
        "data": {
            "type": "stories",
            "attributes": {
                "timezone": "Europe/Brussels",
                "contents": [
                    {
                        "title": "Test",
                        "summary: "Test"
                    }
                ]
            }
        }
    }    
JSON;

const JSON_INVALID_END_DATE = <<<JSON
    {
        "data": {
            "type": "stories",
            "attributes": {
                "publish_date": "2022-01-02 10:00:00",
                "end_date": "22-01-02 10:00:00",
                "timezone": "Europe/Brussels",
                "contents": [
                    {
                        "title": "Test",
                        "summary": "Test"
                    }
                ]
            }
        }
    }
JSON;

const JSON_INVALID_PROMOTION = <<<JSON
    {
        "data": {
            "type": "stories",
            "attributes": {
                "publish_date": "2022-01-02 10:00:00",
                "promotion": "1",
                "timezone": "Europe/Brussels",
                "contents": [
                    {
                        "title": "Test",
                        "summary": "Test"
                    }
                ]
            }
        }
    }
JSON;

const JSON_INVALID_PROMOTION_DATE = <<<JSON
    {
        "data": {
            "type": "stories",
            "attributes": {
                "publish_date": "2022-01-02 10:00:00",
                "promotion_end_date": "22-01-02 10:00:00",
                "timezone": "Europe/Brussels",
                "contents": [
                    {
                        "title": "Test",
                        "summary": "Test"
                    }
                ]
            }
        }
    }
JSON;

use Kwai\Core\Infrastructure\Presentation\InputSchemaProcessor;
use Kwai\Modules\News\Presentation\REST\StorySchema;
use Kwai\Modules\News\UseCases\CreateStoryCommand;
use Kwai\Modules\News\UseCases\UpdateStoryCommand;
use Nette\Schema\ValidationException;

it('throws an exception on invalid data', function ($data) {
    $processor = InputSchemaProcessor::create(new StorySchema(true));
    $processor->process(json_decode($data));
})
    ->with([
        [ "" ],
        JSON_WRONG_TYPE,
        JSON_WRONG_PUBLISH_DATE,
        JSON_MISSING_PUBLISH_DATE,
        JSON_INVALID_END_DATE,
        JSON_INVALID_PROMOTION,
        JSON_INVALID_PROMOTION_DATE
    ])
    ->expectException(ValidationException::class)
;

const JSON_DATA = <<<JSON
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
                }
            }
        }
JSON;

it('can normalize data', function ($data) {
    try {
        $processor = InputSchemaProcessor::create(new StorySchema(true));
        $command = $processor->process(json_decode($data));
        expect($command)->toBeInstanceOf(CreateStoryCommand::class);
    } catch (ValidationException $ve) {
        $this->fail(strval($ve));
    }
})->with([JSON_DATA]);

const JSON_DATA_WITH_ID = <<<JSON
        {
            "data": {
                "type": "stories",
                "id": "126",
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
                }
            }
        }
JSON;

it('can normalize data with an id', function ($data) {
    $schema = new StorySchema();
    try {
        $processor = InputSchemaProcessor::create(new StorySchema());
        $command = $processor->process(json_decode($data));
        expect($command)->toBeInstanceOf(UpdateStoryCommand::class);
    } catch (ValidationException $ve) {
        $this->fail(strval($ve));
    }
})->with([JSON_DATA_WITH_ID]);
