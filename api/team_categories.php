<?php
declare(strict_types=1);

require '../vendor/autoload.php';

use Kwai\Core\Infrastructure\Presentation\PreflightAction;
use REST\Teams\Actions\TeamCategoryBrowseAction;
use REST\Teams\Actions\TeamCategoryCreateAction;
use REST\Teams\Actions\TeamCategoryReadAction;
use REST\Teams\Actions\TeamCategoryUpdateAction;
use Slim\Routing\RouteCollectorProxy;
use function Kwai\Core\Infrastructure\createApplication;

$app = createApplication();

$app->group('/team_categories', function (RouteCollectorProxy $group) {
    $group->options('', PreflightAction::class);
    $group->get('', TeamCategoryBrowseAction::class)
        ->setName('team_categories.browse')
        ->setArgument('auth', 'true')
    ;
    $group->options('/{id:[0-9]+}', PreflightAction::class);
    $group->get('/{id:[0-9]+}', TeamCategoryReadAction::class)
        ->setName('team_categories.read')
        ->setArgument('auth', 'true')
    ;
    $group->post('', TeamCategoryCreateAction::class)
        ->setName('team_categories.create')
        ->setArgument('auth', 'true')
    ;
    $group->patch('/{id:[0-9]+}', TeamCategoryUpdateAction::class)
        ->setName('team_categories.update')
        ->setArgument('auth', 'true')
    ;
});

$app->run();
