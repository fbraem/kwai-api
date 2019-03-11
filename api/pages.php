<?php
require '../src/vendor/autoload.php';

$app = \Core\Clubman::getApplication();

$app->group('/pages', function () {
    $this->get('', \REST\Pages\Actions\BrowseAction::class)
        ->setName('pages.browse')
    ;
    $this->get('/{id:[0-9]+}', \REST\Pages\Actions\ReadAction::class)
        ->setName('pages.read')
    ;
    $this->post('', \REST\Pages\Actions\CreateAction::class)
        ->setName('pages.create')
        ->setArgument('auth', true)
    ;
    $this->patch('/{id:[0-9]+}', \REST\Pages\Actions\UpdateAction::class)
        ->setName('pages.update')
        ->setArgument('auth', true)
    ;
    $this->delete('/{id:[0-9]+}', \REST\Pages\Actions\DeleteAction::class)
        ->setName('pages.delete')
        ->setArgument('auth', true)
    ;
});

$app->run();
