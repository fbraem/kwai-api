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
        ->setName('pagescreate')
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
    $this->post('/{id:[0-9]+}/contents', \REST\Pages\Actions\CreateContentAction::class)
        ->setName('pages.contents.create')
        ->setArgument('auth', true)
    ;
    $this->patch('/{id:[0-9]+}/contents/{contentId:[0-9]+}', \REST\Pages\Actions\UpdateContentAction::class)
        ->setName('pages.contents.update')
        ->setArgument('auth', true)
    ;
});

$app->run();
