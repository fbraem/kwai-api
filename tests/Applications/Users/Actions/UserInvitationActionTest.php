<?php
declare(strict_types=1);

$data = [
    'data' => [
        'type' => 'user_invitations',
        'attributes' => [
            'email' => "jigoro.kano" . rand(1, 100) . "@gmail.com",
            'name' => 'Jigoro Kano',
            'remark' => 'A user invitation created with a unittest'
        ]
    ]
];

it('can create an user invitation', function () use ($data) {
    $response = $this->post('/users/invitations', $data);
    expect($response->getStatusCode())->toBe(200);

    $result = json_decode($response->getContent(), true);
    return $result['data']['id'];
});

it('can get an user invitation', function ($uuid) {
    $response = $this->get('/users/invitations/' . $uuid);
    expect($response->getStatusCode())->toBe(200);
})
    ->depends('it can create an user invitation')
;

it('can confirm a user invitation', function ($uuid) use ($data) {
    unset($data['data']['attributes']['name']);
    $data['data']['id'] = $uuid;
    $data['data']['attributes']['first_name'] = 'Jigoro';
    $data['data']['attributes']['last_name'] = 'Kano';
    $data['data']['attributes']['password'] = 'Test1234';

    $response = $this->post('/users/invitations/' . $uuid, $data);
    expect($response->getStatusCode())->toBe(200);
})
    ->depends('it can create an user invitation')
;

it('can browse user invitations', function () {
    $response = $this->get('/users/invitations');
    expect($response->getStatusCode())->toBe(200);
});
