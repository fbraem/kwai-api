<?php
declare(strict_types=1);

require '../vendor/autoload.php';

use Kwai\Core\Infrastructure\Presentation\PreflightAction;
use REST\Countries\Actions\BrowseAction;
use Slim\Routing\RouteCollectorProxy;
use function Kwai\Core\Infrastructure\createApplication;

$app = createApplication();

$app->options('/countries', PreflightAction::class);
$app->group('/countries', function (RouteCollectorProxy $group) {
    $group->get('', BrowseAction::class)
        ->setName('countries.browse')
    ;
});

$app->run();
