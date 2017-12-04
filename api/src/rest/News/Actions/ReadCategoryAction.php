<?php

namespace REST\News\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\NotFoundResponder;

use League\Fractal;

class ReadCategoryAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $id = $request->getAttribute('route.id');

        $repository = new \Domain\News\NewsCategoryRepository();
        $category = $repository->find($id);
        if (!$category) {
            return (new NotFoundResponder(new Responder(), _("Category doesn't exist.")))->respond();
        }

        $payload->setOutput(new Fractal\Resource\Item($category, new \Domain\News\NewsCategoryTransformer(), 'news_categories'));

        return (new JSONResponder(new Responder(), $payload))->respond();
    }
}
