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
    $this->patch('/{id:[0-9]+}', \REST\Teams\Actions\TeamsUpdateAction::class)
        ->setName('teams.update')
        ->setArgument('auth', true)
    ;
});

$app->run();
