<?php

namespace REST\Teams\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Team\TeamTypesTable;
use Domain\Team\TeamTypeTransformer;

use Respect\Validation\Validator as v;

use Core\Validators\ValidationException;
use Core\Validators\InputValidator;
use Core\Validators\EntityExistValidator;
use REST\Teams\TeamTypeValidator;

use Core\Responses\UnprocessableEntityResponse;
use Core\Responses\ResourceResponse;

class TypeCreateAction
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

            $typesTable = TeamTypesTable::getTableFromRegistry();
            $type = $typesTable->newEntity();
            $type->name = $attributes['name'];
            $type->start_age = $attributes['start_age'];
            $type->end_age = $attributes['end_age'];
            $type->competition = $attributes['competition'] ?? false;
            $type->gender = $attributes['gender'] ?? 0;
            $type->active = $attributes['active'] ?? false;
            $type->remark = $attributes['remark'];

            $typeValidator = (new TeamTypeValidator())->validate($type);

            $typesTable->save($type);

            $route = $request->getAttribute('route');
            if (! empty($route)) {
                $route->setArgument('id', $type->id);
            }

            $response = (new ResourceResponse(
                TeamTypeTransformer::createForItem($type)
            ))($response)->withStatus(201);
        } catch (ValidationException $ve) {
            $response = (new UnprocessableEntityResponse($ve->getErrors()))($response);
        }

        return $response;
    }
}
