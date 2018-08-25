<?php

namespace REST\Teams\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Team\TeamTypesTable;
use Domain\Team\TeamTypeTransformer;

use REST\Teams\TeamTypeInputValidator;
use REST\Teams\TeamTypeValidator;

use Cake\Datasource\Exception\RecordNotFoundException;

class TypeUpdateAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $table = TeamTypesTable::getTableFromRegistry();
        try {
            $type = $table->get($args['id']);
        } catch (RecordNotFoundException $rnfe) {
            return $response->withStatus(404, _("Teamtype doesn't exist"));
        }

        $data = $request->getParsedBody();

        $validator = new TeamTypeInputValidator();
        if (! $validator->validate($data)) {
            return $validator->unprocessableEntityResponse($response);
        }

        $attributes = \JmesPath\search('data.attributes', $data);

        if (isset($attributes['name'])) {
            $type->name = $attributes['name'];
        }
        if (isset($attributes['start_age'])) {
            $type->start_age = $attributes['start_age'];
        }
        if (isset($attributes['end_age'])) {
            $type->end_age = $attributes['end_age'];
        }
        if (isset($attributes['competition'])) {
            $type->competition = $attributes['competition'];
        }
        if (isset($attributes['gender'])) {
            $type->gender = $attributes['gender'];
        }
        if (isset($attributes['active'])) {
            $type->active = $attributes['active'];
        }
        if (isset($attributes['remark'])) {
            $type->remark = $attributes['remark'];
        }

        $validator = new TeamTypeValidator();
        if (! $validator->validate($type)) {
            return $validator->unprocessableEntityResponse($response);
        }

        $table->save($type);

        return (new \Core\ResourceResponse(
            TeamTypeTransformer::createForItem($type)
        ))($response)->withStatus(201);
    }
}
