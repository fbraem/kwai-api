<?php

namespace REST\Trainings\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Training\DefinitionsTable;
use Domain\Training\DefinitionTransformer;

use Respect\Validation\Validator as v;

use Core\Validators\ValidationException;
use Core\Validators\InputValidator;
use Core\Validators\EntityExistValidator;

use Core\Responses\UnprocessableEntityResponse;
use Core\Responses\ResourceResponse;
use Core\Responses\NotFoundResponse;

class DefinitionUpdateAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        $definitionsTable = DefinitionsTable::getTableFromRegistry();

        try {
            $def = $definitionsTable->get(
                $args['id'],
                [
                    'contain' => [ 'Season' ]
                ]
            );

            (new InputValidator(
                [
                    'data.attributes.name' => v::notEmpty()->length(1, 255),
                    'data.attributes.description' => v::notEmpty(),
                    'data.attributes.location' => v::length(1, 255),
                    'data.attributes.weekday' => v::intVal()->min(1)->max(7),
                    'data.attributes.start_time' => v::date('H:i'),
                    'data.attributes.end_time' => v::date('H:i')
                ],
                true
            ))->validate($data);

            $seasonData = \JmesPath\search('data.relationships.season.data', $data);
            if (isset($seasonData)) {
                $season = (new EntityExistValidator('data.relationships.season', $definitionsTable->Season, false))->validate($data);
                if ($season) {
                    $def->season = $season;
                } else {
                    $def->season_id = null;
                    $def->season = null;
                }
            }

            $attributes = \JmesPath\search('data.attributes', $data);

            if (isset($attributes['name'])) {
                $def->name = $attributes['name'];
            }
            if (isset($attributes['description'])) {
                $def->description = $attributes['description'];
            }
            if (isset($attributes['weekday'])) {
                $def->weekday = $attributes['weekday'];
            }
            if (isset($attributes['start_time'])) {
                $def->start_time = $attributes['start_time'];
            }
            if (isset($attributes['end_time'])) {
                $def->end_time = $attributes['end_time'];
            }
            if (isset($attributes['active'])) {
                $def->active = $attributes['active'];
            }
            if (isset($attributes['location'])) {
                $def->location = $attributes['location'];
            }
            if (isset($attributes['remark'])) {
                $def->remark = $attributes['remark'];
            }
            $def->user = $request->getAttribute('clubman.user');

            (new \REST\Trainings\DefinitionValidator())->validate($def);

            $definitionsTable->save($def);

            $response = (new ResourceResponse(
                DefinitionTransformer::createForItem($def)
            ))($response);
        } catch (ValidationException $ve) {
            $response = (new UnprocessableEntityResponse($ve->getErrors()))($response);
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("Training definition doesn't exist")))($response);
        }

        return $response;
    }
}
