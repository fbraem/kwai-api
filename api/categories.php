<?php
declare(strict_types=1);

require '../src/vendor/autoload.php';

use Kwai\Core\Infrastructure\Presentation\PreflightAction;
use REST\Categories\Actions\BrowseAction;
use REST\Categories\Actions\CreateAction;
use REST\Categories\Actions\ReadAction;
use REST\Categories\Actions\UpdateAction;
use Slim\Routing\RouteCollectorProxy;
use function Kwai\Core\Infrastructure\createApplication;

$app = createApplication();

$app->group('/categories', function (RouteCollectorProxy $group) {
    $group->options('', PreflightAction::class);
    $group->get('', BrowseAction::class)
        ->setName('categories.browse')
    ;
    $group->options('/{id:[0-9]+}', PreflightAction::class);
    $group->get('/{id:[0-9]+}', ReadAction::class)
        ->setName('categories.read')
    ;
    $group->post('', CreateAction::class)
        ->setName('categories.create')
        ->setArgument('auth', 'true')
    ;
    $group->patch('/{id:[0-9]+}', UpdateAction::class)
        ->setName('categories.update')
        ->setArgument('auth', 'true')
    ;
});

$app->run();
