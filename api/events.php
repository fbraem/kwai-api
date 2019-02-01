<?php
require '../src/vendor/autoload.php';

$app = \Core\Clubman::getApplication();

$app->group('/events', function () {
    $this->get('', \REST\Events\Actions\EventBrowseAction::class)
        ->setName('events.browse')
    ;
    $this->get('/{id:[0-9]+}', \REST\Events\Actions\EventReadAction::class)
        ->setName('events.read')
    ;
    $this->post('', \REST\Events\Actions\EventCreateAction::class)
        ->setName('events.create')
        ->setArgument('auth', true)
    ;
    $this->patch('/{id:[0-9]+}', \REST\Events\Actions\EventUpdateAction::class)
        ->setName('events.update')
        ->setArgument('auth', true)
    ;
});

$app->run();
