<?php

namespace REST\Teams\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Team\TeamTypesTable;
use Domain\Team\TeamTypeTransformer;

use REST\Teams\TeamTypeInputValidator;
use REST\Teams\TeamTypeEmptyValidator;
use REST\Teams\TeamTypeValidator;

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

        $validator = new TeamTypeInputValidator();
        if (! $validator->validate($data)) {
            return $validator->unprocessableEntityResponse($response);
        }
        $validator = new TeamTypeEmptyValidator();
        if (! $validator->validate($data)) {
            return $validator->unprocessableEntityResponse($response);
        }

        $attributes = \JmesPath\search('data.attributes', $data);

        $typesTable = TeamTypesTable::getTableFromRegistry();
        $type = $typesTable->newEntity();
        $type->name = $attributes['name'];
        $type->start_age = $attributes['start_age'];
        $type->end_age = $attributes['end_age'];
        $type->competition = $attributes['competition'];
        $type->gender = $attributes['gender'];
        $type->active = $attributes['active'];
        $type->remark = $attributes['remark'];

        $validator = new TeamTypeValidator();
        if (! $validator->validate($type)) {
            return $validator->unprocessableEntityResponse($response);
        }

        $typesTable->save($type);

        return (new \Core\ResourceResponse(
            TeamTypeTransformer::createForItem($type)
        ))($response)->withStatus(201);
    }
}
