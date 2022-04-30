<?php

use Kwai\Core\Infrastructure\Dependencies\Settings;
use Tests\HttpClientTrait;

require __DIR__ . '/../vendor/autoload.php';

$httpFeature = [
    'Applications/Users/Actions'
];

uses(HttpClientTrait::class)->in(...$httpFeature);

$config = depends('settings', Settings::class);
uses()->beforeEach(
    fn() => $this->withHttpClient('http://api.kwai.com')->login(
        $config->getVariable('KWAI_TEST_USER'),
        $config->getVariable('KWAI_TEST_PASSWORD')
    )
)->in(...$httpFeature);
