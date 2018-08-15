<?php

namespace REST\Pages;

class Router extends \Core\Router
{
    public function createRoutes()
    {
        $this->map->extras(['rest' => 'pages'])
            ->accepts(['application/vnd.api+json']);

        $this->map->get('pages.browse', '/pages', \REST\Pages\Actions\BrowseAction::class);
        $this->map->get('pages.read', '/pages/{id}', \REST\Pages\Actions\ReadAction::class);
        $this->map->post('pages.create', '/pages', \REST\Pages\Actions\CreateAction::class)
            ->auth(['login' => true]);
        $this->map->patch('pages.update', '/pages/{id}', \REST\Pages\Actions\UpdateAction::class)
            ->auth(['login' => true]);
        $this->map->delete('pages.delete', '/pages/{id}', \REST\Pages\Actions\DeleteAction::class)
            ->auth(['login' => true]);

        //$this->map->post('pages.upload', '/pages/image/{id}', \REST\Pages\Actions\UploadAction::class)
        //    ->auth(['login' => true])
        //    ->accepts(['image/*']);

        $this->map->post('createContent', '/pages/{id}/contents', \REST\Pages\Actions\CreateContentAction::class)
            ->auth(['login' => true])
        ;
        $this->map->patch('updateContent', '/pages/{id}/contents/{contentId}', \REST\Pages\Actions\UpdateContentAction::class)
            ->auth(['login' => true])
        ;
        //$this->map->delete('deleteContent', '/pages/{id}/contents/{contentId}', \REST\Pages\Actions\DeleteContentAction::class)
        //    ->auth(['login' => true])
        //;
    }
}
