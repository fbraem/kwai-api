<?php
require '../src/vendor/autoload.php';

$app = \Core\Clubman::getApplication();

$app->group('/team_types', function () {
    $this->get('', \REST\Teams\Actions\TypeBrowseAction::class)
        ->setName('team_types.browse')
        ->setArgument('auth', true)
    ;
    $this->get('/{id:[0-9]+}', \REST\Teams\Actions\TypeReadAction::class)
        ->setName('team_types.read')
        ->setArgument('auth', true)
    ;
    $this->post('', \REST\Teams\Actions\TypeCreateAction::class)
        ->setName('team_types.create')
        ->setArgument('auth', true)
    ;
    /*
    $this->patch('/{id:[0-9]+}', \REST\Seasons\Actions\UpdateAction::class)
        ->setName('seasons.create')
        ->setArgument('auth', true)
    ;
    */
});

$app->run();
