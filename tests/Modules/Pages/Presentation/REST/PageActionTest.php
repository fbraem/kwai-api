<?php
declare(strict_types=1);

$data = <<<JSON
{
    "data": {
        "type": "stories",
        "attributes": {
            "remark": "Created with PageActionTest",
            "contents": [
                {
                    "title": "PageActionTest",
                    "summary": "This page was created with PageActionTest",
                    "content": "This page is created..."
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

it('can create a page', function () use ($data) {
    $response = $this->post('/pages', json_decode($data, true));
    expect($response->getStatusCode())->toBe(200);

    $result = $response->toArray();
    expect($result)
        ->tobeJSONAPIObject('pages')
    ;
    return $result['data']['id'];
});

it('can update a page', function ($id) use ($data) {
    $json = json_decode($data, true);
    $json['data']['id'] = $id;
    $json['data']['attributes']['remark'] = 'Updated with unit test';

    $response = $this->patch("/pages/$id", $json);
    expect($response->getStatusCode())->toBe(200);

    $result = $response->toArray();
    expect($result)
        ->tobeJSONAPIObject('pages')
    ;
})
    ->depends('it can create a page')
;

it('can get a page', function ($id) {
    $response = $this->get("/pages/$id");
    expect($response->getStatusCode())->toBe(200);

    $result = $response->toArray();
    expect($result)
        ->tobeJSONAPIObject('pages')
    ;
})
    ->depends('it can create a page')
;

it('can browse pages', function () {
    $response = $this->get('/pages');
    expect($response->getStatusCode())->toBe(200);
    $result = $response->toArray();
    expect($result)
        ->tobeJSONAPIArray('pages')
    ;
});
