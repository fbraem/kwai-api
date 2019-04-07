<?php

namespace REST\Categories\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cake\Datasource\Exception\RecordNotFoundException;

use Domain\Category\CategoriesTable;
use Domain\Category\CategoryTransformer;

use Respect\Validation\Validator as v;

use Core\Validators\ValidationException;
use Core\Validators\InputValidator;

use \Core\Responses\ResourceResponse;
use \Core\Responses\UnprocessableEntityResponse;

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

        try {
            (new InputValidator(
                [
                    'data.attributes.name' => v::notEmpty()->length(1, 255),
                    'data.attributes.short_description' => v::notEmpty()->length(1, 255)
                ]
            ))->validate($data);

            $attributes = \JmesPath\search('data.attributes', $data);

            $categoriesTable = CategoriesTable::getTableFromRegistry();
            $category = $categoriesTable->newEntity();
            $category->name = $attributes['name'];
            $category->short_description = $attributes['short_description'];
            $category->description = $attributes['description'];
            $category->remark = $attributes['remark'];
            $category->app = $attributes['app'];
            $category->user = $request->getAttribute('clubman.user');
            $categoriesTable->save($category);

            $route = $request->getAttribute('route');
            if (! empty($route)) {
                $route->setArgument('id', $category->id);
            }

            return (new ResourceResponse(
                CategoryTransformer::createForItem($category)
            ))($response)->withStatus(201);
        } catch (ValidationException $ve) {
            return (new UnprocessableEntityResponse(
                $ve->getErrors()
            ))($response);
        }
    }
}
