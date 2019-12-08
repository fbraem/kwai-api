<?php
require '../src/vendor/autoload.php';

$app = \Core\Clubman::getApplication();

$app->group('/team_categories', function () {
    $this->get('', \REST\Teams\Actions\TeamCategoryBrowseAction::class)
        ->setName('team_categories.browse')
        ->setArgument('auth', true)
    ;
    $this->get('/{id:[0-9]+}', \REST\Teams\Actions\TeamCategoryReadAction::class)
        ->setName('team_categories.read')
        ->setArgument('auth', true)
    ;
    $this->post('', \REST\Teams\Actions\TeamCategoryCreateAction::class)
        ->setName('team_categories.create')
        ->setArgument('auth', true)
    ;
    $this->patch('/{id:[0-9]+}', \REST\Teams\Actions\TeamCategoryUpdateAction::class)
        ->setName('team_categories.update')
        ->setArgument('auth', true)
    ;
});

$app->run();
