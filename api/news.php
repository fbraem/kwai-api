<?php
declare(strict_types=1);

require '../src/vendor/autoload.php';

use Kwai\Modules\News\Presentation\Rest\BrowseStoriesAction;
use Kwai\Modules\News\Presentation\Rest\GetArchiveAction;
use REST\News\Actions\CreateStoryAction;
use REST\News\Actions\DeleteStoryAction;
use REST\News\Actions\ReadStoryAction;
use REST\News\Actions\UpdateStoryAction;
use Slim\Routing\RouteCollectorProxy;
use function Kwai\Core\Infrastructure\createApplication;

$app = createApplication();

$app->group('/news', function (RouteCollectorProxy $group) {
    $group->get('/stories', BrowseStoriesAction::class)
        ->setName('news.browse')
    ;
    $group->get('/stories/{id:[0-9]+}', ReadStoryAction::class)
        ->setName('news.read')
    ;
    $group->post('/stories', CreateStoryAction::class)
        ->setName('news.create')
        ->setArgument('auth', 'true')
    ;
    $group->patch('/stories/{id:[0-9]+}', UpdateStoryAction::class)
        ->setName('news.update')
        ->setArgument('auth', 'true')
    ;
    $group->delete('/stories/{id:[0-9]+}', DeleteStoryAction::class)
        ->setName('news.delete')
        ->setArgument('auth', 'true')
    ;
    $group->get('/archive', GetArchiveAction::class)
        ->setName('news.archive')
    ;
});

$app->run();
