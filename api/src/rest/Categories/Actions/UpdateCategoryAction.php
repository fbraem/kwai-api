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

class UpdateCategoryAction implements \Core\ActionInterface
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
                    _("Category doesn't exist.")
                ))->respond();
        }

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

        if (array_key_exists('name', $attributes)) {
            $category->name = $attributes['name'];
        }
        if (array_key_exists('description', $attributes)) {
            $category->description = $attributes['description'];
        }
        if (array_key_exists('remark', $attributes)) {
            $category->remark = $attributes['remark'];
        }
        $category->user = $request->getAttribute('clubman.user');

        $CategoriesTable->save($category);

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
