<?php
require '../src/vendor/autoload.php';

$app = \Core\Clubman::getApplication();

$app->group('/categories', function () {
    $this->get('', \REST\Categories\Actions\BrowseCategoryAction::class)
        ->setName('categories.browse')
    ;
    $this->get('/{id:[0-9]+}', \REST\Categories\Actions\ReadCategoryAction::class)
        ->setName('categories.read')
    ;
    $this->post('', \REST\Categories\Actions\CreateCategoryAction::class)
        ->setName('categories.create')
        ->setArgument('auth', true)
    ;
    $this->patch('/{id:[0-9]+}', \REST\Categories\Actions\UpdateCategoryAction::class)
        ->setName('categories.update')
        ->setArgument('auth', true)
    ;
});

$app->run();
