<?php
require '../src/vendor/autoload.php';

use Slim\Routing\RouteCollectorProxy;

$app = \Kwai\Core\Infrastructure\Clubman::getApplication();

$app->group('/team_categories', function (RouteCollectorProxy $group) {
    $group->get('', \REST\Teams\Actions\TeamCategoryBrowseAction::class)
        ->setName('team_categories.browse')
        ->setArgument('auth', true)
    ;
    $group->get('/{id:[0-9]+}', \REST\Teams\Actions\TeamCategoryReadAction::class)
        ->setName('team_categories.read')
        ->setArgument('auth', true)
    ;
    $group->post('', \REST\Teams\Actions\TeamCategoryCreateAction::class)
        ->setName('team_categories.create')
        ->setArgument('auth', true)
    ;
    $group->patch('/{id:[0-9]+}', \REST\Teams\Actions\TeamCategoryUpdateAction::class)
        ->setName('team_categories.update')
        ->setArgument('auth', true)
    ;
});

$app->run();
