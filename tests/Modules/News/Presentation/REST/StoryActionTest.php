<?php

declare(strict_types=1);

use Kwai\Core\Domain\ValueObjects\Timestamp;

$now = (string) Timestamp::createNow();
$data = <<<JSON
{
    "data": {
        "type": "stories",
        "attributes": {
            "remark": "Created with StoryActionTest",
            "timezone": "Europe/Brussels",
            "publish_date": "${now}",
            "contents": [
                {
                    "title": "StoryActionTest",
                    "summary": "This story was created with StoryActionTest"
                }
            ]
        },
        "relationships": {
            "application": {
                "data": {
                    "type": "applications",
                    "id": "1"
                }
            }
        }
    }
}
JSON;

it('can create a story', function () use ($data) {
    $response = $this->post('/news/stories', json_decode($data, true));
    expect($response->getStatusCode())->toBe(200);
    $result = $response->toArray();
    expect($result)
        ->tobeJSONAPIObject('stories')
    ;
    return $result['data']['id'];
});

it('can update a story', function ($id) use ($data) {
    $json = json_decode($data, true);
    $json['data']['id'] = $id;
    $json['data']['attributes']['remark'] = 'Updated with unit test';

    $response = $this->patch("/news/stories/$id", $json);
    expect($response->getStatusCode())->toBe(200);
    expect($response->toArray())
        ->tobeJSONAPIObject('stories')
    ;
})
    ->depends('it can create a story')
;

it('can get a story', function ($id) {
    $response = $this->get("/news/stories/$id");
    expect($response->getStatusCode())
        ->toBe(200)
    ;

    expect($response->toArray())
        ->tobeJSONAPIObject('stories')
    ;
})
    ->depends('it can create a story')
;

it('can browse stories', function () {
    $response = $this->get('/news/stories');
    expect($response->getStatusCode())
        ->toBe(200)
    ;

    expect($response->toArray())
        ->tobeJSONAPIArray('stories')
    ;
});
