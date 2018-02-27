<?php

namespace Judo\REST\Grades;

class Router extends \Core\Router
{
    public function createRoutes()
    {
        $this->map->get('sport.judo.grades.browse', '/sport/judo/grades', \Judo\REST\Grades\Actions\BrowseAction::class)
            ->accepts(['application/vnd.api+json']);
    }
}
