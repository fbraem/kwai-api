<?php

namespace REST\Categories\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;

class BrowseCategoryAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $categories = \Domain\Category\CategoriesTable::getTableFromRegistry()->find()->all();
        $payload->setOutput(\Domain\Category\CategoryTransformer::createForCollection($categories));
        return (
            new JSONResponder(
                new Responder(),
                $payload
            ))->respond();
    }
}
