<?php
require '../src/vendor/autoload.php';

$app = \Core\Clubman::getApplication();

$app->group('/trainings', function () {
    $this->get('/definitions', \REST\Trainings\Actions\DefinitionBrowseAction::class)
        ->setName('trainings.definitions.browse')
    ;
    $this->get('/definitions/{id:[0-9]+}', \REST\Trainings\Actions\DefinitionReadAction::class)
        ->setName('trainings.definitions.read')
    ;
    $this->post('/definitions', \REST\Trainings\Actions\DefinitionCreateAction::class)
        ->setName('trainings.definitions.create')
        ->setArgument('auth', true)
    ;
    $this->patch('/definitions/{id:[0-9]+}', \REST\Trainings\Actions\DefinitionUpdateAction::class)
        ->setName('trainings.definitions.update')
        ->setArgument('auth', true)
    ;

    $this->get('/coaches', \REST\Trainings\Actions\CoachBrowseAction::class)
        ->setName('trainings.coaches.browse')
    ;
    $this->get('/coaches/{id:[0-9]+}', \REST\Trainings\Actions\CoachReadAction::class)
        ->setName('trainings.coaches.read')
    ;
    $this->post('/coaches', \REST\Trainings\Actions\CoachCreateAction::class)
        ->setName('trainings.coaches.create')
        ->setArgument('auth', true)
    ;
    $this->patch('/coaches/{id:[0-9]+}', \REST\Trainings\Actions\CoachUpdateAction::class)
        ->setName('trainings.coaches.update')
        ->setArgument('auth', true)
    ;
    $this->delete('/coaches/{id:[0-9]+}', \REST\Trainings\Actions\CoachDeleteAction::class)
        ->setName('trainings.coaches.delete')
        ->setArgument('auth', true)
    ;

    $this->get('/events', \REST\Trainings\Actions\EventBrowseAction::class)
        ->setName('trainings.events.browse')
    ;
    $this->get('/events/{id:[0-9]+}', \REST\Trainings\Actions\EventReadAction::class)
        ->setName('trainings.events.read')
    ;
    $this->post('/events', \REST\Trainings\Actions\EventCreateAction::class)
        ->setName('trainings.events.create')
        ->setArgument('auth', true)
    ;
    $this->patch('/events/{id:[0-9]+}', \REST\Trainings\Actions\EventUpdateAction::class)
        ->setName('trainings.events.update')
        ->setArgument('auth', true)
    ;
    $this->get('/events/{id:[0-9]+}/coaches', \REST\Trainings\Actions\EventCoachBrowseAction::class)
        ->setName('trainings.events.coaches.browse')
        ->setArgument('auth', true)
    ;
    $this->post('/events/{id:[0-9]+}/coaches', \REST\Trainings\Actions\EventCoachCreateAction::class)
        ->setName('trainings.events.coaches.create')
        ->setArgument('auth', true)
    ;
});

$app->run();
