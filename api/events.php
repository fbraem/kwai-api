<?php
declare(strict_types=1);

require '../src/vendor/autoload.php';

use Kwai\Core\Infrastructure\Presentation\PreflightAction;
use REST\Events\Actions\EventBrowseAction;
use REST\Events\Actions\EventCreateAction;
use REST\Events\Actions\EventReadAction;
use REST\Events\Actions\EventUpdateAction;
use Slim\Routing\RouteCollectorProxy;
use function Kwai\Core\Infrastructure\createApplication;

$app = createApplication();

$app->group('/events', function (RouteCollectorProxy $group) {
    $group->options('', PreflightAction::class);
    $group->get('', EventBrowseAction::class)
        ->setName('events.browse')
    ;
    $group->options('/{id:[0-9]+}', PreflightAction::class);
    $group->get('/{id:[0-9]+}', EventReadAction::class)
        ->setName('events.read')
    ;
    $group->post('', EventCreateAction::class)
        ->setName('events.create')
        ->setArgument('auth', 'true')
    ;
    $group->patch('/{id:[0-9]+}', EventUpdateAction::class)
        ->setName('events.update')
        ->setArgument('auth', 'true')
    ;
});

$app->run();
