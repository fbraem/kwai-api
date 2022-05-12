<?php
declare(strict_types=1);

it('can browse user permissions', function () {
    $response = $this->get('/users/permissions');
    expect($response->getStatusCode())->toBe(200);

    $result = json_decode($response->getContent(false), true);
    dd($result);
});
