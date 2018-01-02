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
        $this->map->delete('deleteStory', '/news/stories/{id}', \REST\News\Actions\DeleteStoryAction::class)
            ->auth(['login' => true])
            ->accepts(['application/vnd.api+json']);
        $this->map->post('upload', '/news/image/{id}', \REST\News\Actions\UploadAction::class)
            ->auth(['login' => true]);
        $this->map->post('uploadEmbeddImage', '/news/embedded_image/{id}', \REST\News\Actions\UploadEmbeddedAction::class)
            ->auth(['login' => true]);
        $this->map->get('archive', '/news/archive', \REST\News\Actions\ArchiveAction::class)
            ->accepts(['application/vnd.api+json'])
            ->extras(['format' => 'json']);
    }
}
