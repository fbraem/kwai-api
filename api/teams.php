<?php
declare(strict_types=1);

require '../src/vendor/autoload.php';

use Kwai\Core\Infrastructure\Presentation\PreflightAction;
use REST\Teams\Actions\TeamAvailableMembersBrowseAction;
use REST\Teams\Actions\TeamBrowseAction;
use REST\Teams\Actions\TeamCreateAction;
use REST\Teams\Actions\TeamMembersAddAction;
use REST\Teams\Actions\TeamMembersBrowseAction;
use REST\Teams\Actions\TeamMembersDeleteAction;
use REST\Teams\Actions\TeamReadAction;
use REST\Teams\Actions\TeamUpdateAction;
use Slim\Routing\RouteCollectorProxy;
use function Kwai\Core\Infrastructure\createApplication;

$app = createApplication();

$app->group('/teams', function (RouteCollectorProxy $group) {
    $group->options('', PreflightAction::class);
    $group->get('', TeamBrowseAction::class)
        ->setName('teams.browse')
        ->setArgument('auth', 'true')
    ;
    $group->options('/{id:[0-9]+}', PreflightAction::class);
    $group->get('/{id:[0-9]+}', TeamReadAction::class)
        ->setName('teams.read')
        ->setArgument('auth', 'true')
    ;
    $group->post('', TeamCreateAction::class)
        ->setName('teams.create')
        ->setArgument('auth', 'true')
    ;
    $group->patch('/{id:[0-9]+}', TeamUpdateAction::class)
        ->setName('teams.update')
        ->setArgument('auth', 'true')
    ;
    $group->options('/{id:[0-9]+}/members', PreflightAction::class);
    $group->get('/{id:[0-9]+}/members', TeamMembersBrowseAction::class)
        ->setName('teams.members.browse')
        ->setArgument('auth', 'true')
    ;
    $group->options('/{id:[0-9]+}/available_members', PreflightAction::class);
    $group->get('/{id:[0-9]+}/available_members', TeamAvailableMembersBrowseAction::class)
        ->setName('teams.available_members.browse')
        ->setArgument('auth', 'true')
    ;
    $group->post('/{id:[0-9]+}/members', TeamMembersAddAction::class)
        ->setName('teams.members.add')
        ->setArgument('auth', 'true')
    ;
    $group->delete('/{id:[0-9]+}/members', TeamMembersDeleteAction::class)
        ->setName('teams.members.delete')
        ->setArgument('auth', 'true')
    ;
});

$app->run();
