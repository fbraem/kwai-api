<?php
declare(strict_types=1);

require '../src/vendor/autoload.php';

use REST\Persons\Actions\BrowseAction;
use Slim\Routing\RouteCollectorProxy;
use function Kwai\Core\Infrastructure\createApplication;

$app = createApplication();

$app->group('/persons', function (RouteCollectorProxy $group) {
    $group->get('', BrowseAction::class)
        ->setName('persons.browse')
        ->setArgument('auth', 'true')
    ;
});

$app->run();
