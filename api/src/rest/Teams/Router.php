<?php

namespace REST\Teams;

class Router extends \Core\Router
{
    public function createRoutes()
    {
        $this->map->extras(['rest' => 'teams'])
            ->accepts(['application/vnd.api+json']);

        $this->map->get('team.browse', '/teams', \REST\Teams\Actions\TeamBrowseAction::class)
        ;

        $this->map->get('team_types.browse', '/teams/types', \REST\Teams\Actions\TypeBrowseAction::class)
        ;

        $this->map->get('team_types.read', '/teams/types/{id}', \REST\Teams\Actions\TypeReadAction::class)
        ;

        $this->map->post('team_types.create', '/teams/types', \REST\Teams\Actions\TypeCreateAction::class)
            ->auth(['login' => true])
        ;

        $this->map->patch('team_types.update', '/teams/types/{id}', \REST\Teams\Actions\TypeUpdateAction::class)
            ->auth(['login' => false])
        ;
    }
}
