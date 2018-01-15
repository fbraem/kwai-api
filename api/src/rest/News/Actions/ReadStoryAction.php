<?php

namespace REST\News\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\NotFoundResponder;

use League\Fractal;

class ReadStoryAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $id = $request->getAttribute('route.id');
        $db = $request->getAttribute('clubman.container')['db'];

        $stories = new \Domain\News\NewsStoriesTable($db);
        try {
            $story = $stories->whereId($id)->findOne();
        } catch (\Domain\NotFoundException $nfe) {
            return (new NotFoundResponder(new Responder(), _("Story doesn't exist.")))->respond();
        }

        $filesystem = $request->getAttribute('clubman.container')['filesystem'];
        $payload->setOutput(new Fractal\Resource\Item($story, new \Domain\News\NewsStoryTransformer($filesystem), 'news_stories'));

        return (new JSONResponder(new Responder(), $payload))->respond();
    }
}
