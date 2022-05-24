<?php
declare(strict_types=1);

// TODO: Make sure that there is a member and make sure the coach isn't
// already linked to that member...
$data = <<<JSON
{
    "data": {
        "type": "coaches",
        "attributes": {
            "active": false,
            "diploma": "No diploma",
            "bio": "This coach is created in a test",
            "remark": "Created with CoachActionTest"
        },
        "relationships": {
            "member": {
                "data": {
                    "type": "members",
                    "id": "9"
                }
            }
        }
    }
}
JSON;

it('can create a coach', function () use ($data) {
    $response = $this->post('/coaches', json_decode($data, true));
    expect($response->getStatusCode())
        ->toBe(200)
    ;
    $result = $response->toArray();
    expect($result)
        ->tobeJSONAPIObject('coaches')
    ;
    return $result['data']['id'];
});

it('can update a coach', function ($id) use ($data) {
    $json = json_decode($data, true);
    $json['data']['id'] = $id;
    $json['data']['attributes']['remark'] = 'Updated with unit test';

    $response = $this->patch("/coaches/$id", $json);

    expect($response->getStatusCode())
        ->toBe(200)
    ;

    expect($response->toArray())
        ->tobeJSONAPIObject('coaches')
    ;
})
    ->depends('it can create a coach')
;

it('can get a coach', function ($id) {
    $response = $this->get("/coaches/$id");
    expect($response->getStatusCode())
        ->toBe(200)
    ;

    expect($response->toArray())
        ->tobeJSONAPIObject('coaches')
    ;
})
    ->depends('it can create a coach')
;

it('can browse coaches', function () {
    $response = $this->get('/coaches');
    expect($response->getStatusCode())
        ->toBe(200)
    ;

    expect($response->toArray())
        ->tobeJSONAPIArray('coaches')
    ;
});
