<?php

namespace REST\Persons;

class Router extends \Core\Router
{
    public function createRoutes()
    {
        $this->map->get('persons.browse', '/persons', \REST\Persons\Actions\BrowseAction::class)
            ->accepts(['application/vnd.api+json'])
            ->auth(['login' => true]);
        ;
    }
}
