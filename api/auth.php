<?php
declare(strict_types=1);

require '../src/vendor/autoload.php';

use Slim\Routing\RouteCollectorProxy;
use function Kwai\Core\Infrastructure\createApplication;

$app = createApplication();

$app->group('/auth', function (RouteCollectorProxy $group) {
    /*
        $group->get('/authorize', \REST\Auth\Actions\AuthorizeAction::class)
            ->setName('auth.authorize')
        ;
    */
    $group->post('/login', Kwai\Modules\Users\Presentation\Rest\LoginAction::class)
        ->setName('auth.login')
    ;
    $group->post('/access_token', Kwai\Modules\Users\Presentation\Rest\RefreshTokenAction::class)
        ->setName('auth.access_token')
    ;
    $group->post('/logout', Kwai\Modules\Users\Presentation\Rest\LogoutAction::class)
        ->setName('auth.logout')
        ->setArgument('auth', 'true')
    ;
    $group->get('/users', Kwai\Modules\Users\Presentation\Rest\UserAction::class)
        ->setArgument('auth', 'true')
        ->setName('auth.users')
    ;
});

$app->run();
