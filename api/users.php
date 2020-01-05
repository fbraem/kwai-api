<?php
require '../src/vendor/autoload.php';

use Slim\Routing\RouteCollectorProxy;

$app = \Core\Clubman::getApplication();

$app->group('/users', function (RouteCollectorProxy $group) {
    $group->get('', \REST\Users\Actions\BrowseAction::class)
        ->setName('users.browse')
        ->setArgument('auth', true)
    ;
    $group->get('/{id:[0-9]+}', \REST\Users\Actions\ReadAction::class)
        ->setName('users.read')
        ->setArgument('auth', true)
    ;
    // Rules
    $group->get('/{id:[0-9]+}/abilities', \REST\Users\Actions\UserAbilityBrowseAction::class)
        ->setName('user.abilities.browse')
        ->setArgument('auth', true)
    ;
    $group->patch('/{id:[0-9]+}/abilities/{ability:[0-9]+}', \REST\Users\Actions\UserAttachAbilityAction::class)
        ->setName('user.abilities.attach')
        ->setArgument('auth', true)
    ;
    $group->delete('/{id:[0-9]+}/abilities/{ability:[0-9]+}', \REST\Users\Actions\UserDetachAbilityAction::class)
        ->setName('user.abilities.detach')
        ->setArgument('auth', true)
    ;

    $group->get('/abilities', \REST\Users\Actions\AbilityBrowseAction::class)
        ->setName('users.abilities.browse')
        ->setArgument('auth', true)
    ;
    $group->post('/abilities', \REST\Users\Actions\AbilityCreateAction::class)
        ->setName('users.abilities.create')
        ->setArgument('auth', true)
    ;
    $group->get('/abilities/{id:[0-9]+}', \REST\Users\Actions\AbilityReadAction::class)
        ->setName('users.abilities.read')
        ->setArgument('auth', true)
    ;
    $group->patch('/abilities/{id:[0-9]+}', \REST\Users\Actions\AbilityUpdateAction::class)
        ->setName('users.abilities.update')
        ->setArgument('auth', true)
    ;
    $group->get('/rules', \REST\Users\Actions\RuleBrowseAction::class)
        ->setName('users.rules.browse')
        ->setArgument('auth', true)
    ;

    // Invitations
    $group->post('/invitations', \REST\Users\Actions\CreateInvitationAction::class)
        ->setName('users.invitations.create')
        ->setArgument('auth', true)
    ;
    $group->get('/invitations', \REST\Users\Actions\BrowseInvitationAction::class)
        ->setName('users.invitations.browse')
        ->setArgument('auth', true)
    ;
    $group->get('/invitations/{token:[0-9a-zA-Z]+}', \REST\Users\Actions\ReadInvitationByTokenAction::class)
        ->setName('users.invitations.token')
    ;

    $group->post('/{token:[0-9a-zA-Z]+}', \REST\Users\Actions\CreateWithTokenAction::class)
        ->setName('users.create.token')
    ;
});

$app->run();
