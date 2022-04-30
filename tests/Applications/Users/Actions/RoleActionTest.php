<?php
declare(strict_types=1);

$data = [
    'data' => [
        'type' => 'roles',
        'attributes' => [
            'name' => 'Unittest Role',
            'remark' => 'Created with a unit test'
        ]
    ]
];

it('can create a role', function () use ($data) {
    $response = $this->post('/users/roles', $data);
    expect($response->getStatusCode())->toBe(200);

    $result = json_decode($response->getContent(false), true);
    return $result['data']['id'];
});

it('can update a role', function ($id) use ($data) {
    $data['data']['id'] = $id;
    $data['data']['attributes']['remark'] = 'Updated with unit test';

    $response = $this->patch('/users/roles/' . $id, $data);
    expect($response->getStatusCode())->toBe(200);
})
    ->depends('it can create a role')
;

it('can get a role', function ($id) {
    $response = $this->get('/users/roles/' . $id);
    expect($response->getStatusCode())->toBe(200);
})
    ->depends('it can create a role')
;

it('can browse roles', function () {
    $response = $this->get('/users/roles');
    expect($response->getStatusCode())->toBe(200);
})
;
