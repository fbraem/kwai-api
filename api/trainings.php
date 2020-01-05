<?php
require '../src/vendor/autoload.php';

use Slim\Routing\RouteCollectorProxy;

$app = \Core\Clubman::getApplication();

$app->group('/trainings', function (RouteCollectorProxy $group) {
    $group->get('/definitions', \REST\Trainings\Actions\DefinitionBrowseAction::class)
        ->setName('trainings.definitions.browse')
    ;
    $group->get('/definitions/{id:[0-9]+}', \REST\Trainings\Actions\DefinitionReadAction::class)
        ->setName('trainings.definitions.read')
    ;
    $group->post('/definitions', \REST\Trainings\Actions\DefinitionCreateAction::class)
        ->setName('trainings.definitions.create')
        ->setArgument('auth', true)
    ;
    $group->patch('/definitions/{id:[0-9]+}', \REST\Trainings\Actions\DefinitionUpdateAction::class)
        ->setName('trainings.definitions.update')
        ->setArgument('auth', true)
    ;

    $group->get('/coaches', \REST\Trainings\Actions\CoachBrowseAction::class)
        ->setName('trainings.coaches.browse')
    ;
    $group->get('/coaches/{id:[0-9]+}', \REST\Trainings\Actions\CoachReadAction::class)
        ->setName('trainings.coaches.read')
    ;
    $group->post('/coaches', \REST\Trainings\Actions\CoachCreateAction::class)
        ->setName('trainings.coaches.create')
        ->setArgument('auth', true)
    ;
    $group->patch('/coaches/{id:[0-9]+}', \REST\Trainings\Actions\CoachUpdateAction::class)
        ->setName('trainings.coaches.update')
        ->setArgument('auth', true)
    ;
    $group->delete('/coaches/{id:[0-9]+}', \REST\Trainings\Actions\CoachDeleteAction::class)
        ->setName('trainings.coaches.delete')
        ->setArgument('auth', true)
    ;

    $group->get('', \REST\Trainings\Actions\TrainingBrowseAction::class)
        ->setName('trainings.browse')
    ;
    $group->get('/{id:[0-9]+}', \REST\Trainings\Actions\TrainingReadAction::class)
        ->setName('trainings.read')
    ;
    $group->post('', \REST\Trainings\Actions\TrainingCreateAction::class)
        ->setName('trainings.create')
        ->setArgument('auth', true)
    ;
    $group->patch('/{id:[0-9]+}', \REST\Trainings\Actions\TrainingUpdateAction::class)
        ->setName('trainings.update')
        ->setArgument('auth', true)
    ;
    /*
        $group->get('/{id:[0-9]+}/coaches', \REST\Trainings\Actions\TrainingCoachBrowseAction::class)
            ->setName('trainings.training.coaches.browse')
            ->setArgument('auth', true)
        ;
    */
    /*
        $group->post('/{id:[0-9]+}/coaches', \REST\Trainings\Actions\TrainingCoachCreateAction::class)
            ->setName('trainings.training.coaches.create')
            ->setArgument('auth', true)
        ;
    */
    $group->post('/{id:[0-9]+}/presences', \REST\Trainings\Actions\PresenceCreateAction::class)
        ->setName('trainings.training.presences.create')
        ->setArgument('auth', true)
    ;
});

$app->run();
