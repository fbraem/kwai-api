<?php
require '../src/vendor/autoload.php';

use Slim\Routing\RouteCollectorProxy;

$app = \Core\Clubman::getApplication();

$app->group('/events', function (RouteCollectorProxy $group) {
    $group->get('', \REST\Events\Actions\EventBrowseAction::class)
        ->setName('events.browse')
    ;
    $group->get('/{id:[0-9]+}', \REST\Events\Actions\EventReadAction::class)
        ->setName('events.read')
    ;
    $group->post('', \REST\Events\Actions\EventCreateAction::class)
        ->setName('events.create')
        ->setArgument('auth', true)
    ;
    $group->patch('/{id:[0-9]+}', \REST\Events\Actions\EventUpdateAction::class)
        ->setName('events.update')
        ->setArgument('auth', true)
    ;
});

$app->run();
