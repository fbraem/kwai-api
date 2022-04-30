<?php
declare(strict_types=1);

it('can browse user accounts', function () {
    $response = $this->get('/users/accounts');
    expect($response->getStatusCode())->toBe(200);
});
