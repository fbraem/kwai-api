<?php
require '../src/vendor/autoload.php';

use Slim\Routing\RouteCollectorProxy;

$app = \Kwai\Core\Infrastructure\Clubman::getApplication();

$app->group('/news', function (RouteCollectorProxy $group) {
    $group->get('/stories', \REST\News\Actions\BrowseStoryAction::class)
        ->setName('news.browse')
    ;
    $group->get('/stories/{id:[0-9]+}', \REST\News\Actions\ReadStoryAction::class)
        ->setName('news.read')
    ;
    $group->post('/stories', \REST\News\Actions\CreateStoryAction::class)
        ->setName('news.create')
        ->setArgument('auth', true)
    ;
    $group->patch('/stories/{id:[0-9]+}', \REST\News\Actions\UpdateStoryAction::class)
        ->setName('news.update')
        ->setArgument('auth', true)
    ;
    $group->delete('/stories/{id:[0-9]+}', \REST\News\Actions\DeleteStoryAction::class)
        ->setName('news.delete')
        ->setArgument('auth', true)
    ;
    $group->get('/archive', \REST\News\Actions\ArchiveAction::class)
        ->setName('news.archive')
    ;
});

$app->run();
