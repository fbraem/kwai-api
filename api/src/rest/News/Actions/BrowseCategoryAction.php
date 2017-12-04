<?php

namespace REST\News\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;

use Analogue\ORM\System\Manager;

use League\Fractal;

class BrowseCategoryAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $repository = new \Domain\News\NewsCategoryRepository();
        $categories = $repository->all();
        $payload->setOutput(new Fractal\Resource\Collection($categories, new \Domain\News\NewsCategoryTransformer(), 'news_categories'));

        return (new JSONResponder(new Responder(), $payload))->respond();
    }
}
