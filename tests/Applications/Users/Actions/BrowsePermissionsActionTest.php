<?php
declare(strict_types=1);

it('can browse user permissions', function () {
    $response = $this->get('/users/permissions');
    expect($response->getStatusCode())->toBe(200);

    $data = $response->toArray();
    expect($data)
        ->toHaveKey('data')
    ;
    expect($data['data'])
        ->toBeArray()
        ->toHaveKey('type')
        ->toHaveKey('id')
        ->toHaveKey('attributes')
    ;
    expect($data['data']['type'])
        ->toBe('rules')
    ;
    expect($data['data']['attributes'])
        ->toBeArray()
    ;
});
