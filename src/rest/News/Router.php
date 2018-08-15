<?php

namespace REST\News;

class Router extends \Core\Router
{
    public function createRoutes()
    {
        $this->map->extras(['rest' => 'news'])
            ->accepts(['application/vnd.api+json']);

        $this->map->get('browseStory', '/news/stories', \REST\News\Actions\BrowseStoryAction::class);
        $this->map->get('readStory', '/news/stories/{id}', \REST\News\Actions\ReadStoryAction::class);
        $this->map->post('createStory', '/news/stories', \REST\News\Actions\CreateStoryAction::class)
            ->auth(['login' => true])
        ;
        $this->map->patch('updateStory', '/news/stories/{id}', \REST\News\Actions\UpdateStoryAction::class)
            ->auth(['login' => true])
        ;
        $this->map->delete('deleteStory', '/news/stories/{id}', \REST\News\Actions\DeleteStoryAction::class)
            ->auth(['login' => true])
        ;

        //$this->map->post('upload', '/news/image/{id}', \REST\News\Actions\UploadAction::class)
        //    ->auth(['login' => true])
        //    ->accepts(['image/*'])
        //;
        //$this->map->post('uploadEmbeddImage', '/news/embedded_image/{id}', \REST\News\Actions\UploadEmbeddedAction::class)
        //     ->auth(['login' => true]);
        $this->map->get('archive', '/news/archive', \REST\News\Actions\ArchiveAction::class);

        $this->map->post('createContent', '/news/stories/{id}/contents', \REST\News\Actions\CreateContentAction::class)
            ->auth(['login' => true])
        ;
        $this->map->patch('updateContent', '/news/stories/{id}/contents/{contentId}', \REST\News\Actions\UpdateContentAction::class)
            ->auth(['login' => true])
        ;
        //$this->map->delete('deleteContent', '/news/stories/{id}/contents/{contentId}', \REST\News\Actions\DeleteContentAction::class)
        //    ->auth(['login' => true])
        //;
    }
}
