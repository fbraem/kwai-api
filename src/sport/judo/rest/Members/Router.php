<?php

namespace Judo\REST\Members;

class Router extends \Core\Router
{
    public function createRoutes()
    {
        $this->map->get('sport.judo.members.browse', '/sport/judo/members', \Judo\REST\Members\Actions\BrowseAction::class)
            ->accepts(['application/vnd.api+json'])
            //->auth(['login' => true])
        ;
        $this->map->post('sport.judo.members.upload', '/sport/judo/members/upload', \Judo\REST\Members\Actions\UploadAction::class)
            ->accepts(['application/vnd.api+json'])
            //->auth(['login' => true])
        ;
    }
}
