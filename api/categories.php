<?php
require '../src/vendor/autoload.php';

$app = \Core\Clubman::getApplication();

$app->group('/categories', function () {
    $this->get('', \REST\Categories\Actions\BrowseAction::class)
        ->setName('categories.browse')
    ;
    $this->get('/{id:[0-9]+}', \REST\Categories\Actions\ReadAction::class)
        ->setName('categories.read')
    ;
    $this->post('', \REST\Categories\Actions\CreateAction::class)
        ->setName('categories.create')
        ->setArgument('auth', true)
    ;
    $this->patch('/{id:[0-9]+}', \REST\Categories\Actions\UpdateAction::class)
        ->setName('categories.update')
        ->setArgument('auth', true)
    ;
});

$app->run();
