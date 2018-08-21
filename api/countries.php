<?php
require '../src/vendor/autoload.php';

$app = \Core\Clubman::getApplication();

$app->group('/countries', function () {
    $this->get('', \REST\Countries\Actions\BrowseAction::class)
        ->setName('countries.browse')
    ;
});

$app->run();
