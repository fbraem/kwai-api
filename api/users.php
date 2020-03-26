<?php
require '../src/vendor/autoload.php';

use Core\Clubman;

use Kwai\Modules\Users\Presentation\Rest\BrowseUserAction;
use Kwai\Modules\Users\Presentation\Rest\CreateUserInvitationAction;
use Kwai\Modules\Users\Presentation\Rest\ConfirmInvitationAction;
use Kwai\Modules\Users\Presentation\Rest\GetUserAction;
use REST\Users\Actions\AbilityBrowseAction;
use REST\Users\Actions\AbilityCreateAction;
use REST\Users\Actions\AbilityReadAction;
use REST\Users\Actions\AbilityUpdateAction;
use REST\Users\Actions\BrowseInvitationAction;
use REST\Users\Actions\ReadInvitationByTokenAction;
use REST\Users\Actions\RuleBrowseAction;
use REST\Users\Actions\UserAbilityBrowseAction;
use REST\Users\Actions\UserAttachAbilityAction;
use REST\Users\Actions\UserDetachAbilityAction;

use Slim\Routing\RouteCollectorProxy;

$app = Clubman::getApplication();

$app->group('/users', function (RouteCollectorProxy $group) {
    $group->get('', BrowseUserAction::class)
        ->setName('users.browse')
        ->setArgument('auth', true)
    ;
    // Rules
    $group->get('/{id:[0-9]+}/abilities', UserAbilityBrowseAction::class)
        ->setName('user.abilities.browse')
        ->setArgument('auth', true)
    ;
    $group->patch('/{id:[0-9]+}/abilities/{ability:[0-9]+}', UserAttachAbilityAction::class)
        ->setName('user.abilities.attach')
        ->setArgument('auth', true)
    ;
    $group->delete('/{id:[0-9]+}/abilities/{ability:[0-9]+}', UserDetachAbilityAction::class)
        ->setName('user.abilities.detach')
        ->setArgument('auth', true)
    ;

    $group->get('/abilities', AbilityBrowseAction::class)
        ->setName('users.abilities.browse')
        ->setArgument('auth', true)
    ;
    $group->post('/abilities', AbilityCreateAction::class)
        ->setName('users.abilities.create')
        ->setArgument('auth', true)
    ;
    $group->get('/abilities/{id:[0-9]+}', AbilityReadAction::class)
        ->setName('users.abilities.read')
        ->setArgument('auth', true)
    ;
    $group->patch('/abilities/{id:[0-9]+}', AbilityUpdateAction::class)
        ->setName('users.abilities.update')
        ->setArgument('auth', true)
    ;
    $group->get('/rules', RuleBrowseAction::class)
        ->setName('users.rules.browse')
        ->setArgument('auth', true)
    ;

    // Invitations
    $group->post('/invitations', CreateUserInvitationAction::class)
        ->setName('users.invitations.create')
        ->setArgument('auth', true)
    ;
    $group->get('/invitations', BrowseInvitationAction::class)
        ->setName('users.invitations.browse')
        ->setArgument('auth', true)
    ;
    $group->get('/invitations/{token:[0-9a-zA-Z\-]+}', ReadInvitationByTokenAction::class)
        ->setName('users.invitations.token')
    ;
    $group->post('/invitations/{uuid:[0-9a-zA-Z\-]+}', ConfirmInvitationAction::class)
        ->setName('users.invitations.confirm')
    ;
    $group->get('/{uuid:[0-9a-zA-Z\-]+}', GetUserAction::class)
        ->setName('users.get')
        ->setArgument('auth', true)
    ;
});

$app->run();
