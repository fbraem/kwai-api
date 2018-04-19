<?php

namespace REST\Teams;

class Router extends \Core\Router
{
    public function createRoutes()
    {
        $this->map->extras(['rest' => 'teams'])
            ->accepts(['application/vnd.api+json']);

        $this->map->get('teams.browse', '/teams', \REST\Teams\Actions\TeamBrowseAction::class)
            ->auth(['login' => true])
        ;

        $this->map->get('teams.read', '/teams/{id}', \REST\Teams\Actions\TeamReadAction::class)
            ->auth(['login' => true])
        ;

        $this->map->get('teams.members.browse', '/teams/{id}/members', \REST\Teams\Actions\TeamMembersBrowseAction::class)
            ->auth(['login' => true])
        ;

        $this->map->get('teams.available_members.browse', '/teams/{id}/available_members', \REST\Teams\Actions\TeamAvailableMembersBrowseAction::class)
        //    ->auth(['login' => true])
        ;

        $this->map->post('teams.members.add', '/teams/{id}/members', \REST\Teams\Actions\TeamMembersAddAction::class)
            ->auth(['login' => true])
        ;

        $this->map->delete('teams.members.delete', '/teams/{id}/members', \REST\Teams\Actions\TeamMembersDeleteAction::class)
            ->auth(['login' => true])
        ;

        $this->map->post('teams.create', '/teams', \REST\Teams\Actions\TeamCreateAction::class)
            ->auth(['login' => true])
        ;

        $this->map->patch('teams.update', '/teams/{id}', \REST\Teams\Actions\TeamUpdateAction::class)
            ->auth(['login' => false])
        ;

        $this->map->get('team_types.browse', '/teams/types', \REST\Teams\Actions\TypeBrowseAction::class)
            ->auth(['login' => true])
        ;

        $this->map->get('team_types.read', '/teams/types/{id}', \REST\Teams\Actions\TypeReadAction::class)
            ->auth(['login' => true])
        ;

        $this->map->post('team_types.create', '/teams/types', \REST\Teams\Actions\TypeCreateAction::class)
            ->auth(['login' => true])
        ;

        $this->map->patch('team_types.update', '/teams/types/{id}', \REST\Teams\Actions\TypeUpdateAction::class)
            ->auth(['login' => false])
        ;
    }
}
