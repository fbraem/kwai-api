<?php
require '../../../src/vendor/autoload.php';

$app = \Core\Clubman::getApplication();

$app->group('/members', function () {
    $this->get('', \Judo\REST\Members\Actions\BrowseAction::class)
        ->setName('sport.judo.members.browse')
    ;
});

$app->run();
