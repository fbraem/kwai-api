<?php
require '../src/vendor/autoload.php';

use Slim\Routing\RouteCollectorProxy;

$app = \Kwai\Core\Infrastructure\Clubman::getApplication();

$app->group('/persons', function (RouteCollectorProxy $group) {
    $group->get('', \REST\Persons\Actions\BrowseAction::class)
        ->setName('persons.browse')
        ->setArgument('auth', true)
    ;
});

$app->run();
