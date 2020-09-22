<?php
require '../../../src/vendor/autoload.php';

use Judo\REST\Members\Actions\BrowseAction;
use Judo\REST\Members\Actions\ReadAction;
use Judo\REST\Members\Actions\TrainingBrowseAction;
use Judo\REST\Members\Actions\UploadAction;
use Slim\Routing\RouteCollectorProxy;
use function Kwai\Core\Infrastructure\createApplication;

$app = createApplication('/api/sport/judo');

$app->group('/members', function (RouteCollectorProxy $group) {
    $group->get('', BrowseAction::class)
        ->setName('sport.judo.members.browse')
        ->setArgument('auth', 'true')
    ;
    $group->get('/{id:[0-9]+}', ReadAction::class)
        ->setName('sport.judo.members')
        ->setArgument('auth', 'true')
    ;
    $group->post('/upload', UploadAction::class)
        ->setName('sport.judo.members.upload')
        ->setArgument('auth', 'true')
    ;
    $group->get('/{id:[0-9]+}/trainings', TrainingBrowseAction::class)
        ->setName('sport.judo.members.trainings')
        ->setArgument('auth', 'true')
    ;
});

$app->run();
