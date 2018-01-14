<?php

namespace REST\News\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\HTTPCodeResponder;
use Core\Responders\NotFoundResponder;

class DeleteStoryAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $id = $request->getAttribute('route.id');
        $db = $request->getAttribute('clubman.container')['db'];

        $storiesTable = new \Domain\News\NewsStoriesTable($db);
        $story = $storiesTable->whereId($id)->findOne();
        if (!$story) {
            return (new NotFoundResponder(new Responder(), _("Story doesn't exist.")))->respond();
        }

        $story->delete();

        $filesystem = $request->getAttribute('clubman.container')['filesystem'];
        $folder = 'images/news/' . $id;
        $filesystem->deleteDir($folder);

        return (new HTTPCodeResponder(new Responder(), 200))->respond();
    }
}
