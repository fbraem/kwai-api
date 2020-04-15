<?php
declare(strict_types=1);

require '../src/vendor/autoload.php';

use REST\Countries\Actions\BrowseAction;
use Slim\Routing\RouteCollectorProxy;
use function Kwai\Core\Infrastructure\createApplication;

$app = createApplication();

$app->group('/countries', function (RouteCollectorProxy $group) {
    $group->get('', BrowseAction::class)
        ->setName('countries.browse')
    ;
});

$app->run();
