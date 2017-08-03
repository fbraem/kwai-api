<?php

namespace REST\Users;

class Router extends \Core\Router
{
    public function createRoutes()
    {
        $this->map->get('browse', '/users', \REST\Users\Actions\BrowseAction::class)
            ->auth(['login' => true])
            ->accepts(['application/vnd.api+json']);
        $this->map->get('read', '/users/{id}', \REST\Users\Actions\ReadAction::class)
            ->auth(['login' => true])
            ->accepts(['application/vnd.api+json']);
    }
}
