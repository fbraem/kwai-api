<?php

declare(strict_types=1);

use Tests\HttpClientTrait;

uses(HttpClientTrait::class);

it('can get an application', function () {
    $response = $this->get('/portal/applications/1');
    expect($response->getStatusCode())->toBe(200);
});
