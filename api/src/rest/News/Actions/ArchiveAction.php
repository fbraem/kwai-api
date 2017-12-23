<?php

namespace REST\News\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\SimpleJSONResponder;

class ArchiveAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $db = $request->getAttribute('clubman.container')['db'];
        $dbStories = new \Domain\News\NewsStoriesTable($db);
        $archive = $dbStories->archive();
        return (new SimpleJSONResponder(new Responder(), $archive))->respond();
    }
}
