<?php

namespace REST\News;

class Router extends \Core\Router
{
    public function createRoutes()
    {
        $this->map->get('browseStory', '/news/stories', \REST\News\Actions\BrowseStoryAction::class)
            ->accepts(['application/vnd.api+json']);
        $this->map->get('readStory', '/news/stories/{id}', \REST\News\Actions\ReadStoryAction::class)
            ->accepts(['application/vnd.api+json']);
        $this->map->post('createStory', '/news/stories', \REST\News\Actions\CreateStoryAction::class)
            ->auth(['login' => true])
            ->accepts(['application/vnd.api+json']);
        $this->map->patch('updateStory', '/news/stories/{id}', \REST\News\Actions\UpdateStoryAction::class)
            ->auth(['login' => true])
            ->accepts(['application/vnd.api+json']);
        $this->map->get('browseCategory', '/news/categories', \REST\News\Actions\BrowseCategoryAction::class)
            ->accepts(['application/vnd.api+json']);
        $this->map->post('upload', '/news/image', \REST\News\Actions\UploadAction::class);
    }
}
