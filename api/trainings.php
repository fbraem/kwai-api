<?php
declare(strict_types=1);

use Kwai\Applications\TrainingsApplication;

require '../src/vendor/autoload.php';

$app = new TrainingsApplication();
$app->run();

/*
use Kwai\Core\Infrastructure\Presentation\PreflightAction;
use REST\Trainings\Actions\CoachBrowseAction;
use REST\Trainings\Actions\CoachCreateAction;
use REST\Trainings\Actions\CoachDeleteAction;
use REST\Trainings\Actions\CoachReadAction;
use REST\Trainings\Actions\CoachUpdateAction;
use REST\Trainings\Actions\DefinitionBrowseAction;
use REST\Trainings\Actions\DefinitionCreateAction;
use REST\Trainings\Actions\DefinitionReadAction;
use REST\Trainings\Actions\DefinitionUpdateAction;
use REST\Trainings\Actions\PresenceCreateAction;
use REST\Trainings\Actions\TrainingBrowseAction;
use REST\Trainings\Actions\TrainingCreateAction;
use REST\Trainings\Actions\TrainingReadAction;
use REST\Trainings\Actions\TrainingUpdateAction;
use REST\Trainings\Actions\UploadPresencesAction;
use Slim\Routing\RouteCollectorProxy;
use function Kwai\Core\Infrastructure\createApplication;

$app = createApplication();

$app->group('/trainings', function (RouteCollectorProxy $group) {
    $group->options('/definitions', PreflightAction::class);
    $group->get('/definitions', DefinitionBrowseAction::class)
        ->setName('trainings.definitions.browse')
    ;
    $group->options('/definitions/{id:[0-9]+}', PreflightAction::class);
    $group->get('/definitions/{id:[0-9]+}', DefinitionReadAction::class)
        ->setName('trainings.definitions.read')
    ;
    $group->post('/definitions', DefinitionCreateAction::class)
        ->setName('trainings.definitions.create')
        ->setArgument('auth', 'true')
    ;
    $group->patch('/definitions/{id:[0-9]+}', DefinitionUpdateAction::class)
        ->setName('trainings.definitions.update')
        ->setArgument('auth', 'true')
    ;

    $group->options('/coaches/{id:[0-9]+}', PreflightAction::class);
    $group->get('/coaches', CoachBrowseAction::class)
        ->setName('trainings.coaches.browse')
    ;
    $group->get('/coaches/{id:[0-9]+}', CoachReadAction::class)
        ->setName('trainings.coaches.read')
    ;
    $group->options('/coaches', PreflightAction::class);
    $group->post('/coaches', CoachCreateAction::class)
        ->setName('trainings.coaches.create')
        ->setArgument('auth', 'true')
    ;
    $group->patch('/coaches/{id:[0-9]+}', CoachUpdateAction::class)
        ->setName('trainings.coaches.update')
        ->setArgument('auth', 'true')
    ;
    $group->delete('/coaches/{id:[0-9]+}', CoachDeleteAction::class)
        ->setName('trainings.coaches.delete')
        ->setArgument('auth', 'true')
    ;

    $group->options('', PreflightAction::class);
    $group->get('', TrainingBrowseAction::class)
        ->setName('trainings.browse')
    ;
    $group->options('/{id:[0-9]+}', PreflightAction::class);
    $group->get('/{id:[0-9]+}', TrainingReadAction::class)
        ->setName('trainings.read')
    ;
    $group->post('', TrainingCreateAction::class)
        ->setName('trainings.create')
        ->setArgument('auth', 'true')
    ;
    $group->patch('/{id:[0-9]+}', TrainingUpdateAction::class)
        ->setName('trainings.update')
        ->setArgument('auth', 'true')
    ;
    $group->options('/{id:[0-9]+}/presences/upload', PreflightAction::class);
    $group->post('/{id:[0-9]+}/presences/upload', UploadPresencesAction::class)
        ->setName('trainings.presences')
        ->setArgument('auth', 'true')
    ;
    $group->options('/{id:[0-9]+}/presences', PreflightAction::class);
    $group->post('/{id:[0-9]+}/presences', PresenceCreateAction::class)
        ->setName('trainings.training.presences.create')
        ->setArgument('auth', 'true')
    ;
});

$app->run();
*/
