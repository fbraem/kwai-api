<?php
declare(strict_types=1);

it('can browse users', function () {
    $response = $this->get('/users');
    expect($response->getStatusCode())
        ->toBe(200)
    ;

    $data = $response->toArray();
    expect($data['meta']['count'])
        ->toBeGreaterThan(0)
    ;
    expect($data)
        ->toHaveKey('data')
    ;
    expect($data['data'])
        ->toBeArray()
    ;
    expect($data['data'][0])
        ->toHaveKey('type', 'users')
        ->toHaveKey('id')
    ;
    return $data['data'][0]['id'];
});

it('can get a user', function ($uuid) {
    $response = $this->get('/users/' . $uuid);
    expect($response->getStatusCode())->toBe(200);

    $data = $response->toArray();
    expect($data)
        ->toHaveKey('data')
    ;
    expect($data['data'])
        ->toHaveKey('id')
    ;
    expect($data['data']['id'])
        ->toBe($uuid)
    ;
})
    ->depends('it can browse users')
;

it('can update a user', function ($uuid) {
    $data = [
        'data' => [
            'type' => 'users',
            'id' => $uuid,
            'attributes' => [
                'first_name' => 'Jigoro',
                'last_name' => 'Kano',
                'remark' => 'Updated with UserActionTest'
            ]
        ]
    ];

    $response = $this->patch('/users/' . $uuid, $data);
    expect($response->getStatusCode())->toBe(200);
})
    ->depends('it can browse users')
;
