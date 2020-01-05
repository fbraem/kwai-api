<?php
require '../src/vendor/autoload.php';

use Slim\Routing\RouteCollectorProxy;

$app = \Core\Clubman::getApplication();

$app->group('/seasons', function (RouteCollectorProxy $group) {
    $group->get('', \REST\Seasons\Actions\BrowseAction::class)
        ->setName('seasons.browse')
        ->setArgument('auth', true)
    ;
    $group->get('/{id:[0-9]+}', \REST\Seasons\Actions\ReadAction::class)
        ->setName('seasons.read')
        ->setArgument('auth', true)
    ;
    $group->post('', \REST\Seasons\Actions\CreateAction::class)
        ->setName('seasons.create')
        ->setArgument('auth', true)
    ;
    $group->patch('/{id:[0-9]+}', \REST\Seasons\Actions\UpdateAction::class)
        ->setName('seasons.create')
        ->setArgument('auth', true)
    ;
});

$app->run();
