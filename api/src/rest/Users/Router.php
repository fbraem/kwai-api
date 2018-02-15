<?php

namespace REST\Users;

class Router extends \Core\Router
{
    public function createRoutes()
    {
        $this->map->extras(['rest' => 'users'])
            ->accepts(['application/vnd.api+json']);

        $this->map->get('users.browse', '/users', \REST\Users\Actions\BrowseAction::class)
            ->auth(['login' => true])
            ->accepts(['application/vnd.api+json']);
        $this->map->get('users.read', '/users/{id}', \REST\Users\Actions\ReadAction::class)
            ->auth(['login' => true])
            ->accepts(['application/vnd.api+json']);
        $this->map->get('users.news', '/users/news/{id}', \REST\Users\Actions\BrowseNewsAction::class)
            //->auth(['login' => true])
            ->accepts(['application/vnd.api+json']);
    }
}
