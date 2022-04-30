<?php
declare(strict_types=1);

it('can browse rules', function () {
    $response = $this->get('/users/rules');
    expect($response->getStatusCode())
        ->toBe(200)
    ;
});
