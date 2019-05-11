<?php
require '../src/vendor/autoload.php';

$app = \Core\Clubman::getApplication();

$app->group('/auth', function () {
    /*
        $this->get('/authorize', \REST\Auth\Actions\AuthorizeAction::class)
            ->setName('auth.authorize')
        ;
    */
    $this->post('/access_token', \REST\Auth\Actions\AccessTokenAction::class)
        ->setName('auth.access_token')
    ;
    $this->post('/logout', \REST\Auth\Actions\LogoutAction::class)
        ->setName('auth.logout')
        ->setArgument('auth', true)
    ;
    $this->get('/users', \REST\Auth\Actions\UserAction::class)
        ->setArgument('auth', true)
        ->setName('auth.users')
    ;
});

$app->run();
