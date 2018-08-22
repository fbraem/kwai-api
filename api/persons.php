<?php
require '../src/vendor/autoload.php';

$app = \Core\Clubman::getApplication();

$app->group('/persons', function () {
    $this->get('', \REST\Persons\Actions\BrowseAction::class)
        ->setName('persons.browse')
        ->setArgument('auth', true)
    ;
});

$app->run();
