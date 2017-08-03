<?php

namespace REST\Auth;

class Router extends \Core\Router
{
    public function createRoutes()
    {
        $this->map->post('login', '/auth/login', \REST\Auth\Actions\LoginAction::class);
            //->accepts(['application/vnd.api+json'])
            //->extras(['format' => 'json']);
        $this->map->get('read', '/auth/logout', \REST\Auth\Actions\LogoutAction::class)
            ->auth(['login' => true]);
            //->accepts(['application/vnd.api+json'])
            //->extras(['format' => 'json']);
    }
}
