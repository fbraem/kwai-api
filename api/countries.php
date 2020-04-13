<?php
require '../src/vendor/autoload.php';

use Slim\Routing\RouteCollectorProxy;

$app = \Kwai\Core\Infrastructure\Clubman::getApplication();

$app->group('/countries', function (RouteCollectorProxy $group) {
    $group->get('', \REST\Countries\Actions\BrowseAction::class)
        ->setName('countries.browse')
    ;
});

$app->run();
