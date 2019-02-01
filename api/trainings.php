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

    $this->get('', \REST\Trainings\Actions\TrainingBrowseAction::class)
        ->setName('trainings.browse')
    ;
    $this->get('/{id:[0-9]+}', \REST\Trainings\Actions\TrainingReadAction::class)
        ->setName('trainings.read')
    ;
    $this->post('', \REST\Trainings\Actions\TrainingCreateAction::class)
        ->setName('trainings.create')
        ->setArgument('auth', true)
    ;
    $this->patch('/{id:[0-9]+}', \REST\Trainings\Actions\TrainingUpdateAction::class)
        ->setName('trainings.update')
        ->setArgument('auth', true)
    ;
    $this->get('/{id:[0-9]+}/coaches', \REST\Trainings\Actions\TrainingCoachBrowseAction::class)
        ->setName('trainings.training.coaches.browse')
        ->setArgument('auth', true)
    ;
    $this->post('/{id:[0-9]+}/coaches', \REST\Trainings\Actions\TrainingCoachCreateAction::class)
        ->setName('trainings.training.coaches.create')
        ->setArgument('auth', true)
    ;
});

$app->run();
