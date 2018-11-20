<?php

namespace REST\Teams\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Team\TeamTypesTable;
use Domain\Team\TeamTypeTransformer;

use Cake\Datasource\Exception\RecordNotFoundException;

use Respect\Validation\Validator as v;

use Core\Validators\ValidationException;
use Core\Validators\InputValidator;
use Core\Validators\EntityExistValidator;
use REST\Teams\TeamTypeValidator;

use Core\Responses\UnprocessableEntityResponse;
use Core\Responses\ResourceResponse;
use Core\Responses\NotFoundResponse;

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
            $data = $request->getParsedBody();
            $type = $table->get($args['id']);

            (new InputValidator([
                'data.attributes.name' => v::notEmpty()->length(1, 255),
                'data.attributes.active' => v::boolType(),
                'data.attributes.start_age' => v::digit(),
                'data.attributes.end_age' => v::digit(),
                'data.attributes.gender' => v::digit()->between(0, 2, true),
                'data.attributes.competition' => v::boolType()
            ], true))->validate($data);

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

            $typeValidator = (new TeamTypeValidator())->validate($type);

            $table->save($type);

            $response = (new ResourceResponse(
                    TeamTypeTransformer::createForItem($type)
            ))($response)->withStatus(201);
        } catch (ValidationException $ve) {
            $response = (new UnprocessableEntityResponse($ve->getErrors()))($response);
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("Teamtype doesn't exist")))($response);
        }

        return $response;
    }
}
