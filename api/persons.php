<?php
declare(strict_types=1);

require '../src/vendor/autoload.php';

use Kwai\Core\Infrastructure\Presentation\PreflightAction;
use REST\Persons\Actions\BrowseAction;
use Slim\Routing\RouteCollectorProxy;
use function Kwai\Core\Infrastructure\createApplication;

$app = createApplication();

$app->group('/persons', function (RouteCollectorProxy $group) {
    $group->options('', PreflightAction::class);
    $group->get('', BrowseAction::class)
        ->setName('persons.browse')
        ->setArgument('auth', 'true')
    ;
});

$app->run();
