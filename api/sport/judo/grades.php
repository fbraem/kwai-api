<?php
require '../../../src/vendor/autoload.php';

use Slim\Routing\RouteCollectorProxy;

$app = \Core\Clubman::getApplication('/api/sport/judo');

$app->group('/grades', function (RouteCollectorProxy $group) {
    $group->get('', \Judo\REST\Grades\Actions\BrowseAction::class)
        ->setName('sport.judo.grades.browse')
    ;
});

$app->run();
