<?php
require '../src/vendor/autoload.php';

use Slim\Routing\RouteCollectorProxy;

$app = \Core\Clubman::getApplication();

$app->group('/auth', function (RouteCollectorProxy $group) {
    /*
        $group->get('/authorize', \REST\Auth\Actions\AuthorizeAction::class)
            ->setName('auth.authorize')
        ;
    */
    $group->post('/access_token', '\Kwai\Modules\Users\Presentation\Rest\AccessTokenAction')
        ->setName('auth.access_token')
    ;
    $group->post('/logout', \REST\Auth\Actions\LogoutAction::class)
        ->setName('auth.logout')
        ->setArgument('auth', true)
    ;
    $group->get('/users', '\Kwai\Modules\Users\Presentation\Rest\UserAction')
        ->setArgument('auth', true)
        ->setName('auth.users')
    ;
});

$app->run();
