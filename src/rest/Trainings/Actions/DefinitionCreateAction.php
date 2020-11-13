<?php

namespace REST\Trainings\Actions;

use Core\Validators\EntityExistValidator;
use Core\Validators\InputValidator;
use Core\Validators\ValidationException;
use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Training\DefinitionsTable;
use Domain\Training\DefinitionTransformer;

use Respect\Validation\Validator as v;

use Kwai\Core\Infrastructure\Presentation\Responses\UnprocessableEntityResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;

class DefinitionCreateAction
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
                    'data.attributes.description' => v::notEmpty(),
                    'data.attributes.location' => [v::length(1, 255), true],
                    'data.attributes.weekday' => v::intVal()->min(1)->max(7),
                    'data.attributes.start_time' => v::date('H:i'),
                    'data.attributes.end_time' => v::date('H:i'),
                    'data.attributes.time_zone' => v::notEmpty()->length(1, 255),
                ]
            ))->validate($data);

            $definitionsTable = DefinitionsTable::getTableFromRegistry();

            $season = (new EntityExistValidator(
                'data.relationships.season',
                $definitionsTable->Season,
                false
            ))->validate($data);

            $team = (new EntityExistValidator(
                'data.relationships.team',
                $definitionsTable->Team,
                false
            ))->validate($data);

            $attributes = \JmesPath\search('data.attributes', $data);

            $def = $definitionsTable->newEntity();
            $def->name = $attributes['name'];
            $def->description = $attributes['description'] ?? null;
            $def->season = $season ?? null;
            $def->team = $team ?? null;
            $def->weekday = $attributes['weekday'];
            $def->start_time = $attributes['start_time'];
            $def->end_time = $attributes['end_time'] ?? null;
            $def->time_zone = $attributes['time_zone'] ?? null;
            $def->active = $attributes['active'] ?? true;
            $def->location = $attributes['location'] ?? null;
            $def->remark = $attributes['remark'] ?? null;
            $def->user = $request->getAttribute('kwai.user')->id();

            (new \REST\Trainings\DefinitionValidator())->validate($def);

            $definitionsTable->save($def);

            $route = $request->getAttribute('route');
            if (! empty($route)) {
                $route->setArgument('id', $def->id);
            }

            $response = (new ResourceResponse(
                DefinitionTransformer::createForItem($def)
            ))($response)->withStatus(201);
        } catch (ValidationException $ve) {
            $response = (new UnprocessableEntityResponse(
                $ve->getErrors()
            ))($response);
        }

        return $response;
    }
}
