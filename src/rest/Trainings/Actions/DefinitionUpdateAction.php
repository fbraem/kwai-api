<?php

namespace REST\Trainings\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Training\DefinitionsTable;
use Domain\Training\DefinitionTransformer;

use Cake\Datasource\Exception\RecordNotFoundException;

use Respect\Validation\Validator as v;

class DefinitionUpdateAction extends \Core\Action
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
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
        } catch (RecordNotFoundException $rnfe) {
            return $response->withStatus(404, _("Training definition doesn't exist"));
        }

        try {
            $this->validate(
                [
                    'data.attributes.name' => v::notEmpty()->length(1, 255),
                    'data.attributes.location' => v::notEmpty()->length(1, 255),
                    'data.attributes.weekday' => v::intVal()->min(1)->max(7),
                    'data.attributes.start_time' => v::date('H:i:s'),
                    'data.attributes.end_time' => v::date('H:i:s')
                ],
                $data,
                true
            );

            $seasonData = \JmesPath\search('data.relationships.season.data', $data);
            if (isset($seasonData)) {
                if ($seasonData['id']) {
                    $def->season = $this->checkEntity('data.relationships.season', $data, $definitionsTable->Season);
                } else {
                    $def->season_id = null;
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

            $definitionsTable->save($def);

            $response = (new \Core\ResourceResponse(
                DefinitionTransformer::createForItem($def)
            ))($response)->withStatus(201);
        } catch (\Core\ValidationException $ve) {
            $response = $ve->unprocessableEntityResponse($response);
        }

        return $response;
    }
}
