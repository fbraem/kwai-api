<?php
declare(strict_types=1);

$data = <<<JSON
{
    "data": {
        "type": "trainings",
        "attributes": {
            "remark": "Created with unit test",
            "contents": [
                {
                    "title": "Middengroep",
                    "summary": "De middengroep traint elke woensdag van 19u tot 20u"
                }
            ],
            "event": {
                "location": "Sporthal Stekene",
                "start_date": "2021-10-20 17:00:00",
                "end_date": "2021-10-20 18:00:00",
                "timezone": "Europe/Brussels",
                "cancelled": false
            }
        },
        "relationships": {
            "definition": {
                "data": {
                    "type": "definitions",
                    "id": "1"
                }
            },
            "teams": {
                "data": [
                    {
                        "type": "teams",
                        "id": "2"
                    }
                ]
            },
            "coaches": {
                "data": [
                    {
                        "type": "coaches",
                        "id": "1"
                    }
                ]
            }    
        }
    }
}
JSON;

it('can create a training', function () use ($data) {
    $response = $this->post('/trainings', json_decode($data, true));
    expect($response->getStatusCode())->toBe(200);

    $result = $response->toArray();
    expect($result)
        ->tobeJSONAPIObject('trainings')
    ;
    return $result['data']['id'];
});

it('can update a training', function ($id) use ($data) {
    $json = json_decode($data, true);
    $json['data']['id'] = $id;
    $json['data']['attributes']['remark'] = 'Updated with unit test';

    $response = $this->patch("/trainings/$id", $json);
    expect($response->getStatusCode())->toBe(200);

    $result = $response->toArray();
    expect($result)
        ->tobeJSONAPIObject('trainings')
    ;
})
    ->depends('it can create a training')
;

it('can get a training', function ($id) {
    $response = $this->get("/trainings/$id");
    expect($response->getStatusCode())->toBe(200);

    $result = $response->toArray();
    expect($result)
        ->tobeJSONAPIObject('trainings')
    ;
})
    ->depends('it can create a training')
;

it('can browse trainings', function () {
    $response = $this->get("/trainings");
    expect($response->getStatusCode())->toBe(200);

    $result = $response->toArray();
    expect($result)
        ->tobeJSONAPIArray('trainings')
    ;
});
