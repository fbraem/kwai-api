<?php

namespace REST\Countries;

class Router extends \Core\Router
{
    public function createRoutes()
    {
        $this->map->get('countries.browse', '/countries', \REST\Countries\Actions\BrowseAction::class)
            ->accepts(['application/vnd.api+json']);
    }
}
