<?php
require '../../../src/vendor/autoload.php';

use Judo\REST\Members\Actions\BrowseAction;
use Judo\REST\Members\Actions\ReadAction;
use Judo\REST\Members\Actions\TrainingBrowseAction;
use Judo\REST\Members\Actions\UploadAction;
use Kwai\Core\Infrastructure\Presentation\PreflightAction;
use Slim\Routing\RouteCollectorProxy;
use function Kwai\Core\Infrastructure\createApplication;

$app = createApplication('/api/sport/judo');

$app->group('/members', function (RouteCollectorProxy $group) {
    $group->options('', PreflightAction::class);
    $group->get('', BrowseAction::class)
        ->setName('sport.judo.members.browse')
        ->setArgument('auth', 'true')
    ;
    $group->options('/{id:[0-9]+}', PreflightAction::class);
    $group->get('/{id:[0-9]+}', ReadAction::class)
        ->setName('sport.judo.members')
        ->setArgument('auth', 'true')
    ;
    $group->options('/upload', PreflightAction::class);
    $group->post('/upload', UploadAction::class)
        ->setName('sport.judo.members.upload')
        ->setArgument('auth', 'true')
    ;
    $group->options('/{id:[0-9]+}/trainings', PreflightAction::class);
    $group->get('/{id:[0-9]+}/trainings', TrainingBrowseAction::class)
        ->setName('sport.judo.members.trainings')
        ->setArgument('auth', 'true')
    ;
});

$app->run();
