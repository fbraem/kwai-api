<?php
require '../src/vendor/autoload.php';

$app = \Core\Clubman::getApplication();

$app->group('/seasons', function () {
    $this->get('', \REST\Seasons\Actions\BrowseAction::class)
        ->setName('seasons.browse')
        ->setArgument('auth', true)
    ;
    $this->get('/{id:[0-9]+}', \REST\Seasons\Actions\ReadAction::class)
        ->setName('seasons.read')
        ->setArgument('auth', true)
    ;
    $this->post('', \REST\Seasons\Actions\CreateAction::class)
        ->setName('seasons.create')
        ->setArgument('auth', true)
    ;
});

$app->run();
