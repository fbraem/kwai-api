<?php

namespace REST\Categories\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cake\Datasource\Exception\RecordNotFoundException;

use Domain\Category\CategoriesTable;
use Domain\Category\CategoryTransformer;

use Core\Validators\ValidationException;
use Core\Validators\InputValidator;

use Respect\Validation\Validator as v;

use Core\Responses\ResourceResponse;
use Core\Responses\UnprocessableEntityResponse;
use Core\Responses\NotFoundResponse;

class UpdateAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $categoriesTable = CategoriesTable::getTableFromRegistry();
        try {
            $category = $categoriesTable->get($args['id']);
        } catch (RecordNotFoundException $rnfe) {
            return (new NotFoundResponse(_("Category doesn't exist")))($response);
        }

        $data = $request->getParsedBody();

        try {
            (new InputValidator(
                [
                    'data.attributes.name' => v::notEmpty()->length(1, 255),
                    'data.attributes.short_description' => v::notEmpty()->length(1, 255)
                ],
                true
            ))->validate($data);

            $attributes = \JmesPath\search('data.attributes', $data);

            if (array_key_exists('name', $attributes)) {
                $category->name = $attributes['name'];
            }
            if (array_key_exists('short_description', $attributes)) {
                $category->short_description = $attributes['short_description'];
            }
            if (array_key_exists('description', $attributes)) {
                $category->description = $attributes['description'];
            }
            if (array_key_exists('app', $attributes)) {
                $category->app = $attributes['app'];
            }
            if (array_key_exists('remark', $attributes)) {
                $category->remark = $attributes['remark'];
            }
            $category->user = $request->getAttribute('clubman.user');

            $categoriesTable->save($category);

            return (new ResourceResponse(
                CategoryTransformer::createForItem(
                    $category,
                    $this->container->get('filesystem')
                )
            ))($response);
        } catch (ValidationException $ve) {
            return (new UnprocessableEntityResponse(
                        $ve->getErrors()
                    ))($response);
        }
    }
}
