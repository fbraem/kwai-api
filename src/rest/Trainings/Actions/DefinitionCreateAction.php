<?php

namespace REST\Trainings\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Training\DefinitionsTable;
use Domain\Training\DefinitionTransformer;

use Respect\Validation\Validator as v;

class DefinitionCreateAction extends \Core\Action
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        try {
            $this->validate(
                [
                    'data.attributes.name' => v::notEmpty()->length(1, 255),
                    'data.attributes.location' => v::notEmpty()->length(1, 255),
                    'data.attributes.weekday' => v::intVal()->min(1)->max(7),
                    'data.attributes.start_time' => v::date('H:i:s'),
                    'data.attributes.end_time' => v::date('H:i:s')
                ],
                $data
            );

            $definitionsTable = DefinitionsTable::getTableFromRegistry();

            $season = $this->checkEntity('data.relationships.season', $data, $definitionsTable->Season);

            $attributes = \JmesPath\search('data.attributes', $data);

            $def = $definitionsTable->newEntity();
            $def->name = $attributes['name'];
            $def->description = $attributes['description'] ?? null;
            $def->season = $season ?? null;
            $def->weekday = $attributes['weekday'];
            $def->start_time = $attributes['start_time'];
            $def->end_time = $attributes['end_time'] ?? null;
            $def->active = $attributes['active'] ?? true;
            $def->location = $attributes['location'] ?? null;
            $def->remark = $attributes['remark'] ?? null;
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
