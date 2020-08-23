<?php
require '../../../src/vendor/autoload.php';

use Judo\REST\Grades\Actions\BrowseAction;
use Slim\Routing\RouteCollectorProxy;
use function Kwai\Core\Infrastructure\createApplication;

$app = createApplication('/api/sport/judo');


$app->group('/grades', function (RouteCollectorProxy $group) {
    $group->get('', BrowseAction::class)
        ->setName('sport.judo.grades.browse')
    ;
});

$app->run();
