<?php

namespace REST\News\Actions;

use Psr\Http\Message\RequestInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\NotFoundResponder;

use League\Fractal;

class ReadStoryAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload)
    {
        $id = $request->getAttribute('route.id');

        $repository = new \Domain\News\NewsStoryRepository();
        $story = $repository->find($id);
        if (!$story) {
            return new NotFoundResponder(new Responder(), _("Story doesn't exist."));
        }

        $payload->setOutput(new Fractal\Resource\Item($story, new \Domain\News\NewsStoryTransformer, 'news_story'));

        return new JSONResponder(new Responder(), $payload);
    }
}
