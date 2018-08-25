<?php
require '../src/vendor/autoload.php';

$app = \Core\Clubman::getApplication();

$app->group('/teams', function () {
    $this->get('', \REST\Teams\Actions\TeamBrowseAction::class)
        ->setName('teams.browse')
        ->setArgument('auth', true)
    ;
    $this->get('/{id:[0-9]+}', \REST\Teams\Actions\TeamReadAction::class)
        ->setName('teams.read')
        ->setArgument('auth', true)
    ;
    $this->post('', \REST\Teams\Actions\TeamCreateAction::class)
        ->setName('teams.create')
        ->setArgument('auth', true)
    ;
    $this->patch('/{id:[0-9]+}', \REST\Teams\Actions\TeamUpdateAction::class)
        ->setName('teams.update')
        ->setArgument('auth', true)
    ;
    $this->get('/{id:[0-9]+}/members', \REST\Teams\Actions\TeamMembersBrowseAction::class)
        ->setName('teams.members.browse')
        ->setArgument('auth', true)
    ;
    $this->get('/{id:[0-9]+}/available_members', \REST\Teams\Actions\TeamAvailableMembersBrowseAction::class)
        ->setName('teams.available_members.browse')
        ->setArgument('auth', true)
    ;
    $this->post('/{id:[0-9]+}/members', \REST\Teams\Actions\TeamMembersAddAction::class)
        ->setName('teams.members.add')
        ->setArgument('auth', true)
    ;
    $this->delete('/{id:[0-9]+}/members', \REST\Teams\Actions\TeamMembersDeleteAction::class)
        ->setName('teams.members.delete')
        ->setArgument('auth', true)
    ;
});

$app->run();
