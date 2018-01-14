<?php

namespace REST\Categories\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\JSONErrorResponder;
use Core\Responders\HTTPCodeResponder;
use Core\Responders\NotFoundResponder;

use League\Fractal;

class UpdateCategoryAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $id = $request->getAttribute('route.id');
        $db = $request->getAttribute('clubman.container')['db'];

        $category = (new \Domain\Category\CategoriesTable($db))->whereId($id)->findOne();
        if (!$category) {
            return (new NotFoundResponder(new Responder(), _("Category doesn't exist.")))->respond();
        }

        $data = $payload->getInput();

        $validator = new \REST\Categories\CategoryValidator();
        $errors = $validator->validate($data);
        if (count($errors) > 0) {
            return (new JSONErrorResponder(new HTTPCodeResponder(new Responder(), 422), $errors))->respond();
        }

        $attributes = \JmesPath\search('data.attributes', $data);

        $category = new \Domain\Category\Category(
            $db,
            array_merge($category->extract(), [
                'name' => $attributes['name'],
                'description' => $attributes['description'],
                'remark' => $attributes['remark'],
                'user' => $request->getAttribute('clubman.user'),
            ])
        );
        $category->store();

        $payload->setOutput(new Fractal\Resource\Item($category, new \Domain\Category\CategoryTransformer(), 'categories'));

        return (new JSONResponder(new HTTPCodeResponder(new Responder(), 201), $payload))->respond();
    }
}
