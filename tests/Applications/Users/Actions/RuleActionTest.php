<?php
declare(strict_types=1);

use Kwai\Core\Infrastructure\Dependencies\Settings;
use Tests\HttpClientTrait;

uses(HttpClientTrait::class);

$config = depends('settings', Settings::class);
beforeEach()
    ->withHttpClient('http://api.kwai.com')
    ->login(
        $config->getVariable('KWAI_TEST_USER'),
        $config->getVariable('KWAI_TEST_PASSWORD')
    )
;

it('can browse rules', function () {
    $response = $this->get('/users/rules');
    expect($response->getStatusCode())
        ->toBe(200)
    ;
});
