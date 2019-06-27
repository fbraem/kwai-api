<?php
require '../src/vendor/autoload.php';

$app = \Core\Clubman::getApplication();

$app->group('/users', function () {
    $this->get('', \REST\Users\Actions\BrowseAction::class)
        ->setName('users.browse')
        ->setArgument('auth', true)
    ;
    $this->get('/{id:[0-9]+}', \REST\Users\Actions\ReadAction::class)
        ->setName('users.read')
        ->setArgument('auth', true)
    ;
    // Rules
    $this->get('/{id:[0-9]+}/abilities', \REST\Users\Actions\UserAbilityBrowseAction::class)
        ->setName('user.abilities.browse')
        ->setArgument('auth', true)
    ;

    $this->get('/abilities', \REST\Users\Actions\AbilityBrowseAction::class)
        ->setName('users.abilities.browse')
        ->setArgument('auth', true)
    ;
    $this->post('/abilities', \REST\Users\Actions\AbilityCreateAction::class)
        ->setName('users.abilities.create')
        ->setArgument('auth', true)
    ;
    $this->get('/abilities/{id:[0-9]+}', \REST\Users\Actions\AbilityReadAction::class)
        ->setName('users.abilities.read')
        ->setArgument('auth', true)
    ;
    $this->get('/rules', \REST\Users\Actions\RuleBrowseAction::class)
        ->setName('users.rules.browse')
        ->setArgument('auth', true)
    ;

    $this->post('/{token:[0-9a-zA-Z]+}', \REST\Users\Actions\CreateWithTokenAction::class)
        ->setName('users.create.token')
    ;
});

$app->run();
