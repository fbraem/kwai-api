<?php
declare(strict_types=1);

it('can browse teams', function () {
    $response = $this->get('/club/teams');
    expect($response->getStatusCode())->toBe(200);

    $data = $response->toArray();
    expect($data)
        ->tobeJSONAPIArray('teams')
    ;
});
