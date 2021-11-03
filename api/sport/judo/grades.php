<?php
require '../../../vendor/autoload.php';

use Judo\REST\Grades\Actions\BrowseAction;
use Slim\Routing\RouteCollectorProxy;
use function Kwai\Core\Infrastructure\createApplication;

$app = createApplication('/api/sport/judo');


$app->group('/grades', function (RouteCollectorProxy $group) {
    $group->options('', PreflightAction::class);
    $group->get('', BrowseAction::class)
        ->setName('sport.judo.grades.browse')
    ;
});

$app->run();
