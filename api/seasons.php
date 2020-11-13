<?php
declare(strict_types=1);

require '../src/vendor/autoload.php';

use Kwai\Core\Infrastructure\Presentation\PreflightAction;
use REST\Seasons\Actions\BrowseAction;
use REST\Seasons\Actions\CreateAction;
use REST\Seasons\Actions\ReadAction;
use REST\Seasons\Actions\UpdateAction;
use Slim\Routing\RouteCollectorProxy;
use function Kwai\Core\Infrastructure\createApplication;

$app = createApplication();

$app->group('/seasons', function (RouteCollectorProxy $group) {
    $group->options('', PreflightAction::class);
    $group->get('', BrowseAction::class)
        ->setName('seasons.browse')
        ->setArgument('auth', 'true')
    ;
    $group->options('/{id:[0-9]+}', PreflightAction::class);
    $group->get('/{id:[0-9]+}', ReadAction::class)
        ->setName('seasons.read')
        ->setArgument('auth', 'true')
    ;
    $group->post('', CreateAction::class)
        ->setName('seasons.create')
        ->setArgument('auth', 'true')
    ;
    $group->patch('/{id:[0-9]+}', UpdateAction::class)
        ->setName('seasons.create')
        ->setArgument('auth', 'true')
    ;
});

$app->run();
