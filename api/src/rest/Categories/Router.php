<?php

namespace REST\Categories;

class Router extends \Core\Router
{
    public function createRoutes()
    {
        $this->map->get('browseCategory', '/categories', \REST\Categories\Actions\BrowseCategoryAction::class)
            ->accepts(['application/vnd.api+json']);
        $this->map->get('readCategory', '/categories/{id}', \REST\Categories\Actions\ReadCategoryAction::class)
            ->accepts(['application/vnd.api+json']);
        $this->map->post('createCategory', '/categories', \REST\Categories\Actions\CreateCategoryAction::class)
            ->auth(['login' => true])
            ->accepts(['application/vnd.api+json']);
        $this->map->patch('updateCategory', '/categories/{id}', \REST\Categories\Actions\UpdateCategoryAction::class)
            ->auth(['login' => true])
            ->accepts(['application/vnd.api+json']);
    }
}
