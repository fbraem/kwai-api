<?php

namespace REST\Categories\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cake\Datasource\Exception\RecordNotFoundException;

use Domain\Category\CategoriesTable;
use Domain\Category\CategoryTransformer;
use REST\Categories\CategoryInputValidator;
use REST\Categories\CategoryEmptyValidator;

class CreateAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        $validator = new CategoryInputValidator();
        if (! $validator->validate($data)) {
            return $validator->unprocessableEntityResponse($response);
        }

        $validator = new CategoryEmptyValidator();
        if (! $validator->validate($data)) {
            return $validator->unprocessableEntityResponse($response);
        }

        $attributes = \JmesPath\search('data.attributes', $data);

        $categoriesTable = CategoriesTable::getTableFromRegistry();
        $category = $categoriesTable->newEntity();
        $category->name = $attributes['name'];
        $category->description = $attributes['description'];
        $category->remark = $attributes['remark'];
        $category->user = $request->getAttribute('clubman.user');
        $categoriesTable->save($category);

        return (new \Core\ResourceResponse(
            CategoryTransformer::createForItem($category)
        ))($response)->withStatus(201);
    }
}
