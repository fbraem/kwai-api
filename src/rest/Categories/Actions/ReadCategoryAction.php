<?php

namespace REST\Categories\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\NotFoundResponder;

class ReadCategoryAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $id = $request->getAttribute('route.id');

        try {
            $category = \Domain\Category\CategoriesTable::getTableFromRegistry()->get($id);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
            return (
                new NotFoundResponder(
                    new Responder(),
                    _("Season doesn't exist.")
                ))->respond();
        }
        $payload->setOutput(\Domain\Category\CategoryTransformer::createForItem($category));
        return (
            new JSONResponder(
                new Responder(),
                $payload
            ))->respond();
    }
}
