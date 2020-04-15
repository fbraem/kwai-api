<?php
declare(strict_types=1);

require '../src/vendor/autoload.php';

use REST\Pages\Actions\BrowseAction;
use REST\Pages\Actions\CreateAction;
use REST\Pages\Actions\DeleteAction;
use REST\Pages\Actions\ReadAction;
use REST\Pages\Actions\UpdateAction;
use Slim\Routing\RouteCollectorProxy;
use function Kwai\Core\Infrastructure\createApplication;

$app = createApplication();

$app->group('/pages', function (RouteCollectorProxy $group) {
    $group->get('', BrowseAction::class)
        ->setName('pages.browse')
    ;
    $group->get('/{id:[0-9]+}', ReadAction::class)
        ->setName('pages.read')
    ;
    $group->post('', CreateAction::class)
        ->setName('pages.create')
        ->setArgument('auth', 'true')
    ;
    $group->patch('/{id:[0-9]+}', UpdateAction::class)
        ->setName('pages.update')
        ->setArgument('auth', 'true')
    ;
    $group->delete('/{id:[0-9]+}', DeleteAction::class)
        ->setName('pages.delete')
        ->setArgument('auth', 'true')
    ;
});

$app->run();
