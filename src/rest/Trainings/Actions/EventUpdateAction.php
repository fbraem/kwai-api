<?php

namespace REST\Trainings\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Training\EventsTable;
use Domain\Training\EventTransformer;

use Cake\Datasource\Exception\RecordNotFoundException;

use Respect\Validation\Validator as v;

use Core\Validators\ValidationException;
use Core\Validators\InputValidator;
use Core\Validators\EntityExistValidator;

use Core\Responses\UnprocessableEntityResponse;
use Core\Responses\ResourceResponse;
use Core\Responses\NotFoundResponse;

class EventUpdateAction
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
            $table = EventsTable::getTableFromRegistry();

            $event = $table->get($args['id'], [
                'contain' => ['TrainingDefinition', 'Season', 'Coaches' ]
            ]);

            (new InputValidator(
                [
                    'data.attributes.name' => v::length(1, 255),
                    'data.attributes.start_date' => v::date('Y-m-d'),
                    'data.attributes.start_time' => v::date('H:i:s'),
                    'data.attributes.end_time' => v::date('H:i:s'),
                    'data.attributes.time_zone' => v::length(1, 255),
                    'data.attributes.active' => v::boolType(),
                    'data.attributes.location' => v::length(1, 255)
                ],
                true
            ))->validate($data);

            $seasonData = \JmesPath\search('data.relationships.season.data', $data);
            if (isset($seasonData)) {
                $event->season = (new EntityExistValidator(
                    'data.relationships.season',
                    $table->Season,
                    true
                ))->validate($data);
            }

            $defData = \JmesPath\search('data.relationships.training_definition.data', $data);
            if (isset($defData)) {
                $def = (new EntityExistValidator('data.relationships.training_definition', $table->TrainingDefinition, false))->validate($data);
                if ($def) {
                    $event->training_definition = $def;
                } else {
                    $def->training_definition_id = null;
                    $def->training_definition = null;
                }
            }

            $attributes = \JmesPath\search('data.attributes', $data);

            if (isset($attributes['name'])) {
                $event->name = $attributes['name'];
            }
            if (isset($attributes['description'])) {
                $event->description = $attributes['description'];
            }
            if (isset($attributes['start_date'])) {
                $event->start_date = $attributes['start_date'];
            }
            if (isset($attributes['start_time'])) {
                $event->start_time = $attributes['start_time'];
            }
            if (isset($attributes['end_time'])) {
                $event->end_time = $attributes['end_time'];
            }
            if (isset($attributes['time_zone'])) {
                $event->time_zone = $attributes['time_zone'];
            }
            if (isset($attributes['active'])) {
                $event->active = $attributes['active'];
            }
            if (isset($attributes['remark'])) {
                $event->remark = $attributes['remark'];
            }

            $event->user = $request->getAttribute('clubman.user');

            (new \REST\Trainings\EventValidator())->validate($event);

            $table->save($event);

            $route = $request->getAttribute('route');
            if (! empty($route)) {
                $route->setArgument('id', $event->id);
            }

            $response = (new ResourceResponse(
                EventTransformer::createForItem($event)
            ))($response)->withStatus(201);
        } catch (ValidationException $ve) {
            $response = (new UnprocessableEntityResponse(
                $ve->getErrors()
            ))($response);
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("Event doesn't exist")))($response);
        }

        return $response;
    }
}
