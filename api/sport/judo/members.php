<?php
require '../../../src/vendor/autoload.php';

$app = \Core\Clubman::getApplication();

$app->group('/members', function () {
    $this->get('', \Judo\REST\Members\Actions\BrowseAction::class)
        ->setName('sport.judo.members.browse')
        ->setArgument('auth', true)
    ;
    $this->post('/members/upload', \Judo\REST\Members\Actions\UploadAction::class)
        ->setName('sport.judo.members.upload')
        ->setArgument('auth', true)
    ;
});

$app->run();
