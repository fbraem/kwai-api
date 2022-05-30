<?php
declare(strict_types=1);

$data = [
    'data' => [
        'type' => 'definitions',
        'attributes' => [
            'name' => 'Test',
            'description' => 'Created while running unit test',
            'weekday' => 1,
            'start_time' => '19:00',
            'end_time' => '21:00',
            'time_zone' => 'Europe/Brussels'
        ]
    ]
];

it('can create a definition', function () use ($data) {
    $response = $this->post('/trainings/definitions', $data);
    expect($response->getStatusCode())->toBe(200);

    $result = $response->toArray();
    expect($result)
        ->tobeJSONAPIObject('definitions')
    ;

    return $result['data']['id'];
});

it('can update a definition', function ($id) use ($data) {
    $data['data']['id'] = $id;
    $data['data']['attributes']['description'] = 'Updated with test "can update a definition"';

    $response = $this->patch("/trainings/definitions/$id", $data);
    expect($response->getStatusCode())->toBe(200);

    $result = $response->toArray();
    expect($result)
        ->tobeJSONAPIObject('definitions')
    ;

})
    ->depends('it can create a definition')
;

it('can get a definition', function ($id) {
    $response = $this->get("/trainings/definitions/$id");
    expect($response->getStatusCode())->toBe(200);
    $result = $response->toArray();
    expect($result)
        ->tobeJSONAPIObject('definitions')
    ;
})
    ->depends('it can create a definition')
;

it('can browse definitions', function () {
    $response = $this->get("/trainings/definitions");
    expect($response->getStatusCode())->toBe(200);
    $result = $response->toArray();
    expect($result)
        ->tobeJSONAPIArray('definitions')
    ;

});
