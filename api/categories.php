<?php
require '../src/vendor/autoload.php';

$app = \Core\Clubman::getSlimApplication();

$app->add(new \Core\Middlewares\AuthenticationMiddleware($app->getContainer()));

$app->group('/categories', function () {
    $this->get('', \REST\Categories\Actions\BrowseCategoryAction::class);
    $this->get('/{id:[0-9]+}', \REST\Categories\Actions\ReadCategoryAction::class);
    $this->post('', \REST\Categories\Actions\CreateCategoryAction::class)
        ->setArgument('auth', true);
    $this->patch('/{id:[0-9]+}', \REST\Categories\Actions\UpdateCategoryAction::class)
        ->setArgument('auth', true);
});

$app->run();
