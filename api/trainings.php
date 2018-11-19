<?php
require '../src/vendor/autoload.php';

$app = \Core\Clubman::getApplication();

$app->group('/trainings', function () {
    $this->get('/definitions', \REST\Trainings\Actions\DefinitionBrowseAction::class)
        ->setName('trainings.definitions.browse')
    ;
    $this->get('/definitions/{id:[0-9]+}', \REST\Trainings\Actions\DefinitionReadAction::class)
        ->setName('trainings.definitions.read')
    ;
    $this->post('/definitions', \REST\Trainings\Actions\DefinitionCreateAction::class)
        ->setName('trainings.definitions.create')
        ->setArgument('auth', true)
    ;
    $this->patch('/definitions/{id:[0-9]+}', \REST\Trainings\Actions\DefinitionUpdateAction::class)
        ->setName('trainings.definitions.update')
        ->setArgument('auth', true)
    ;
    /*
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
    */
});

$app->run();
