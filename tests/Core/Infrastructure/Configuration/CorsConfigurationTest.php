<?php
declare(strict_types=1);

use Dotenv\Exception\ValidationException;
use Kwai\Core\Infrastructure\Configuration\Configuration;

it('can configure CORS', function () {
    $config = Configuration::createFromString("KWAI_CORS_PORT=81\nKWAI_CORS_HOST=api.kwai.com");
    $corsConfig = $config->getCorsConfiguration();
    $corsSettings = $corsConfig->createCorsSettings();

    expect($corsSettings->getServerOriginPort())
        ->toBe(81)
    ;
    expect($corsSettings->getServerOriginHost())
        ->toBe('api.kwai.com')
    ;
    expect($corsSettings->getServerOriginScheme())
        ->toBe('http')
    ;
});

it('validates the port', function () {
    $config = Configuration::createFromString("KWAI_CORS_PORT=xxx");
    $config->getCorsConfiguration();
})->expectException(ValidationException::class);

it('can configure an allowed origin', function () {
    $config = Configuration::createFromString("KWAI_CORS_HOST=api.kwai.com\nKWAI_CORS_ORIGIN=ui.kwai.com");
    $corsConfig = $config->getCorsConfiguration();
    $corsSettings = $corsConfig->createCorsSettings();

    expect($corsSettings->isRequestOriginAllowed('ui.kwai.com'))
        ->toBeTrue()
    ;
});

it('can configure an multiple allowed origins', function () {
    $config = Configuration::createFromString("KWAI_CORS_HOST=api.kwai.com\nKWAI_CORS_ORIGIN=ui.kwai.com,test.kwai.com");
    $corsConfig = $config->getCorsConfiguration();
    $corsSettings = $corsConfig->createCorsSettings();

    expect($corsSettings->isRequestOriginAllowed('ui.kwai.com'))
        ->toBeTrue()
    ;
    expect($corsSettings->isRequestOriginAllowed('test.kwai.com'))
        ->toBeTrue()
    ;
    expect($corsSettings->isRequestOriginAllowed('example.kwai.com'))
        ->toBeFalse()
    ;
});

it('can configure multiple methods', function () {
    $config = Configuration::createFromString("KWAI_CORS_HOST=api.kwai.com\nKWAI_CORS_METHODS=GET,POST");
    $corsConfig = $config->getCorsConfiguration();
    $corsSettings = $corsConfig->createCorsSettings();

    expect($corsSettings->isRequestMethodSupported('GET'))
        ->toBeTrue()
    ;
    expect($corsSettings->isRequestMethodSupported('POST'))
        ->toBeTrue()
    ;
});

