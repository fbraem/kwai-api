<?php

namespace REST\News\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;

use League\Fractal;

class BrowseCategoryAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $db = $request->getAttribute('clubman.container')['db'];
        $categories = (new \Domain\News\NewsCategoriesTable($db))->find();
        $payload->setOutput(new Fractal\Resource\Collection($categories, new \Domain\News\NewsCategoryTransformer(), 'news_categories'));

        return (new JSONResponder(new Responder(), $payload))->respond();
    }
}
