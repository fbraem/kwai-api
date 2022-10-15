<?php

use Kwai\Core\Infrastructure\Dependencies\Settings;
use Tests\HttpClientTrait;

require __DIR__ . '/../vendor/autoload.php';

$httpFeature = [
    'Applications/Auth/Actions',
    'Applications/Trainings/Actions',
    'Applications/Users/Actions',
    'Modules/Applications/Presentation/REST',
    'Modules/Club/Presentation/REST',
    'Modules/Coaches/Presentation/REST',
    'Modules/Mails/Presentation/REST',
    'Modules/News/Presentation/REST',
    'Modules/Pages/Presentation/REST',
];

uses(HttpClientTrait::class)->in(...$httpFeature);

$config = depends('settings', Settings::class);
uses()->beforeEach(
    fn() => $this->withHttpClient('http://api.kwai.com')->login(
        $config->getVariable('KWAI_TEST_USER'),
        $config->getVariable('KWAI_TEST_PASSWORD')
    )
)->in(...$httpFeature);
