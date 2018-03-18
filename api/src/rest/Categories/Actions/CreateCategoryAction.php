<?php

namespace REST\Categories\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\JSONErrorResponder;
use Core\Responders\HTTPCodeResponder;

class CreateCategoryAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $data = $payload->getInput();

        $validator = new \REST\Categories\CategoryValidator();
        $errors = $validator->validate($data);
        if (count($errors) > 0) {
            return (
                new JSONErrorResponder(
                    new HTTPCodeResponder(
                        new Responder(),
                        422
                    ),
                    $errors
                ))->respond();
        }

        $attributes = \JmesPath\search('data.attributes', $data);

        $categoriesTable = \Domain\Category\CategoriesTable::getTableFromRegistry();
        $category = $categoriesTable->newEntity();
        $category->name = $attributes['name'];
        $category->description = $attributes['description'];
        $category->remark = $attributes['remark'];
        $category->user = $request->getAttribute('clubman.user');
        $categoriesTable->save($category);

        $payload->setOutput(\Domain\Category\CategoryTransformer::createForItem($category));

        return (
            new JSONResponder(
                new HTTPCodeResponder(
                    new Responder(),
                    201
                ),
                $payload
            ))->respond();
    }
}
