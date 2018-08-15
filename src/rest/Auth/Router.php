<?php

namespace REST\Auth;

class Router extends \Core\Router
{
    public function createRoutes()
    {
        $this->map->extras(['rest' => 'auth']);

        $this->map->post('auth.token', '/auth/access_token', \REST\Auth\Actions\AccessTokenAction::class);
        $this->map->post('auth.logout', '/auth/logout', \REST\Auth\Actions\LogoutAction::class)
            ->auth(['login' => true]);
        $this->map->get('auth.authorize', '/auth/authorize', \REST\Auth\Actions\AuthorizeAction::class);
    }
}
