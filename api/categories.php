<?php
require '../src/vendor/autoload.php';

use Slim\Routing\RouteCollectorProxy;

$app = \Core\Clubman::getApplication();

$app->group('/categories', function (RouteCollectorProxy $group) {
    $group->get('', \REST\Categories\Actions\BrowseAction::class)
        ->setName('categories.browse')
    ;
    $group->get('/{id:[0-9]+}', \REST\Categories\Actions\ReadAction::class)
        ->setName('categories.read')
    ;
    $group->post('', \REST\Categories\Actions\CreateAction::class)
        ->setName('categories.create')
        ->setArgument('auth', true)
    ;
    $group->patch('/{id:[0-9]+}', \REST\Categories\Actions\UpdateAction::class)
        ->setName('categories.update')
        ->setArgument('auth', true)
    ;
});

$app->run();
