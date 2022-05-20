<?php

declare(strict_types=1);

it('execute BrowseMembersAction', function ()  {
    $response = $this->get('/club/members');
    expect($response->getStatusCode())->toBe(200);

    $data = $response->toArray();
    expect($data)
        ->tobeJSONAPIArray('members', true)
    ;
});
