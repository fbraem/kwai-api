<?php

namespace REST\Teams\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Team\TeamCategoriesTable;
use Domain\Team\TeamCategoryTransformer;

use Respect\Validation\Validator as v;

use Core\Validators\ValidationException;
use Core\Validators\InputValidator;
use Core\Validators\EntityExistValidator;
use REST\Teams\TeamCategoryValidator;

use Core\Responses\UnprocessableEntityResponse;
use Core\Responses\ResourceResponse;

class TeamCategoryCreateAction
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
            (new InputValidator([
                'data.attributes.name' => v::notEmpty()->length(1, 255),
                'data.attributes.active' => [ v::boolType(), true ],
                'data.attributes.start_age' => [ v::digit(), true ],
                'data.attributes.end_age' => [ v::digit(), true ],
                'data.attributes.gender' => [ v::digit()->between(0, 2, true), true ],
                'data.attributes.competition' => [ v::boolType(), true ]
            ]))->validate($data);

            $attributes = \JmesPath\search('data.attributes', $data);

            $categoriesTable = TeamCategoriesTable::getTableFromRegistry();
            $category = $categoriesTable->newEntity();
            $category->name = $attributes['name'];
            $category->start_age = $attributes['start_age'];
            $category->end_age = $attributes['end_age'];
            $category->competition = $attributes['competition'] ?? false;
            $category->gender = $attributes['gender'] ?? 0;
            $category->active = $attributes['active'] ?? false;
            $category->remark = $attributes['remark'];

            $categoryValidator = (new TeamCategoryValidator())->validate($category);

            $categoriesTable->save($category);

            $route = $request->getAttribute('route');
            if (! empty($route)) {
                $route->setArgument('id', $category->id);
            }

            $response = (new ResourceResponse(
                TeamCategoryTransformer::createForItem($category)
            ))($response)->withStatus(201);
        } catch (ValidationException $ve) {
            $response = (new UnprocessableEntityResponse($ve->getErrors()))($response);
        }

        return $response;
    }
}
