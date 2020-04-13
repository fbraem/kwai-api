<?php
require '../../../src/vendor/autoload.php';

use Slim\Routing\RouteCollectorProxy;

$app = \Kwai\Core\Infrastructure\Clubman::getApplication('/api/sport/judo');

$app->group('/members', function (RouteCollectorProxy $group) {
    $group->get('', \Judo\REST\Members\Actions\BrowseAction::class)
        ->setName('sport.judo.members.browse')
        ->setArgument('auth', true)
    ;
    $group->get('/{id:[0-9]+}', \Judo\REST\Members\Actions\ReadAction::class)
        ->setName('sport.judo.members')
        ->setArgument('auth', true)
    ;
    $group->post('/upload', \Judo\REST\Members\Actions\UploadAction::class)
        ->setName('sport.judo.members.upload')
        ->setArgument('auth', true)
    ;
    $group->get('/{id:[0-9]+}/trainings', \Judo\REST\Members\Actions\TrainingBrowseAction::class)
        ->setName('sport.judo.members.trainings')
        ->setArgument('auth', true)
    ;
});

$app->run();
