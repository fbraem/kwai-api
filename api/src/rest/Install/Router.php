<?php

namespace REST\Install;

class Router extends \Core\Router
{
    public function createRoutes()
    {
        $this->map->post('create', '/install', \REST\Install\Actions\CreateAction::class)
            ->accepts(['application/vnd.api+json'])
            ->extras(['format' => 'json']);
    }
}
