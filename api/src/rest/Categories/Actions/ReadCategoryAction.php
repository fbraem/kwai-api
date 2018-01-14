<?php

namespace REST\Categories\Actions;

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
        $db = $request->getAttribute('clubman.container')['db'];

        $category = (new \Domain\Category\CategoriesTable($db))->whereId($id)->findOne();
        if (!$category) {
            return (new NotFoundResponder(new Responder(), _("Category doesn't exist.")))->respond();
        }

        $payload->setOutput(new Fractal\Resource\Item($category, new \Domain\Category\CategoryTransformer(), 'categories'));

        return (new JSONResponder(new Responder(), $payload))->respond();
    }
}
