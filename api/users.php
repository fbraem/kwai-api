<?php
require '../src/vendor/autoload.php';

$app = \Core\Clubman::getApplication();

$app->group('/users', function () {
    $this->get('', \REST\Users\Actions\BrowseAction::class)
        ->setName('users.browse')
        ->setArgument('auth', true)
    ;
    $this->get('/{id:[0-9]+}', \REST\Users\Actions\ReadAction::class)
        ->setName('users.read')
        ->setArgument('auth', true)
    ;
    $this->post('/{token:[0-9a-zA-Z]+}', \REST\Users\Actions\CreateWithTokenAction::class)
        ->setName('users.create.token')
    ;
});

$app->run();
