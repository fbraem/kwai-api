<?php
declare(strict_types=1);

it('can recover a user', function() {
    $response = $this->post('/users/recoveries', [ 'email' => 'franky.braem@gmail.com' ]);
    expect($response->getStatusCode())->toBe(200);

    $result = json_decode($response->getContent(), true);
    expect($result)->toBeArray();
});
