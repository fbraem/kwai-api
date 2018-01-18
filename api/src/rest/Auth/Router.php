<?php

namespace REST\Auth;

class Router extends \Core\Router
{
    public function createRoutes()
    {
        $this->map->extras(['rest' => 'auth']);

        $this->map->post('token', '/auth/access_token', \REST\Auth\Actions\AccessTokenAction::class);
        $this->map->post('logout', '/auth/logout', \REST\Auth\Actions\LogoutAction::class)
            ->auth(['login' => true]);
        //->accepts(['application/vnd.api+json'])
        //->extras(['format' => 'json']);
        $this->map->get('authorize', '/auth/authorize', \REST\Auth\Actions\AuthorizeAction::class);
    }
}
