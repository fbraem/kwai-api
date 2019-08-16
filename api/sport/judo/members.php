<?php
require '../../../src/vendor/autoload.php';

$app = \Core\Clubman::getApplication();

$app->group('/members', function () {
    $this->get('', \Judo\REST\Members\Actions\BrowseAction::class)
        ->setName('sport.judo.members.browse')
        ->setArgument('auth', true)
    ;
    $this->get('/{id:[0-9]+}', \Judo\REST\Members\Actions\ReadAction::class)
        ->setName('sport.judo.members')
        ->setArgument('auth', true)
    ;
    $this->post('/upload', \Judo\REST\Members\Actions\UploadAction::class)
        ->setName('sport.judo.members.upload')
        ->setArgument('auth', true)
    ;
    $this->get('/{id:[0-9]+}/trainings', \Judo\REST\Members\Actions\TrainingBrowseAction::class)
        ->setName('sport.judo.members.trainings')
        ->setArgument('auth', true)
    ;
});

$app->run();
