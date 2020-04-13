<?php
require '../src/vendor/autoload.php';

use Slim\Routing\RouteCollectorProxy;

$app = \Kwai\Core\Infrastructure\Clubman::getApplication();

$app->group('/pages', function (RouteCollectorProxy $group) {
    $group->get('', \REST\Pages\Actions\BrowseAction::class)
        ->setName('pages.browse')
    ;
    $group->get('/{id:[0-9]+}', \REST\Pages\Actions\ReadAction::class)
        ->setName('pages.read')
    ;
    $group->post('', \REST\Pages\Actions\CreateAction::class)
        ->setName('pages.create')
        ->setArgument('auth', true)
    ;
    $group->patch('/{id:[0-9]+}', \REST\Pages\Actions\UpdateAction::class)
        ->setName('pages.update')
        ->setArgument('auth', true)
    ;
    $group->delete('/{id:[0-9]+}', \REST\Pages\Actions\DeleteAction::class)
        ->setName('pages.delete')
        ->setArgument('auth', true)
    ;
});

$app->run();
