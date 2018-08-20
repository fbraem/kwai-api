<?php
require '../src/vendor/autoload.php';

$app = \Core\Clubman::getApplication();

$app->group('/news', function () {
    $this->get('/stories', \REST\News\Actions\BrowseStoryAction::class)
        ->setName('news.browse')
    ;
    $this->get('/stories/{id:[0-9]+}', \REST\News\Actions\ReadStoryAction::class)
        ->setName('news.read')
    ;
    $this->post('/stories', \REST\News\Actions\CreateStoryAction::class)
        ->setName('news.create')
        ->setArgument('auth', true)
    ;
    $this->patch('/stories/{id:[0-9]+}', \REST\News\Actions\UpdateStoryAction::class)
        ->setName('news.update')
        ->setArgument('auth', true)
    ;
    $this->delete('/stories/{id:[0-9]+}', \REST\News\Actions\DeleteStoryAction::class)
        ->setName('news.delete')
        ->setArgument('auth', true)
    ;
    $this->get('/archive', \REST\News\Actions\ArchiveAction::class)
        ->setName('news.archive')
    ;
    $this->post('/stories/{id:[0-9]+}/contents', \REST\News\Actions\CreateContentAction::class)
        ->setName('news.contents.create')
        ->setArgument('auth', true)
    ;
    $this->patch('/stories/{id:[0-9]+}/contents/{contentId:[0-9]+}', \REST\News\Actions\UpdateContentAction::class)
        ->setName('news.contents.update')
        ->setArgument('auth', true)
    ;
});

$app->run();
