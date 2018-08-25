<?php
require '../../../src/vendor/autoload.php';

$app = \Core\Clubman::getApplication();

$app->group('/grades', function () {
    $this->get('', \Judo\REST\Grades\Actions\BrowseAction::class)
        ->setName('sport.judo.grades.browse')
    ;
});

$app->run();
