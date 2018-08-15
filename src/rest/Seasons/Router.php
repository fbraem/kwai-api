<?php

namespace REST\Seasons;

class Router extends \Core\Router
{
    public function createRoutes()
    {
        $this->map->extras(['rest' => 'seasons'])
            ->accepts(['application/vnd.api+json']);

        $this->map->get('seasons.browse', '/seasons', \REST\Seasons\Actions\BrowseAction::class)
        ;

        $this->map->get('seasons.read', '/seasons/{id}', \REST\Seasons\Actions\ReadAction::class)
        ;

        $this->map->post('seasons.create', '/seasons', \REST\Seasons\Actions\CreateAction::class)
            ->auth(['login' => true])
        ;

        $this->map->patch('seasons.update', '/seasons/{id}', \REST\Seasons\Actions\UpdateAction::class)
            ->auth(['login' => true])
        ;
    }
}
