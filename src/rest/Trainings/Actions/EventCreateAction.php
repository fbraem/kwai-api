<?php

namespace REST\Trainings\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Training\EventsTable;
use Domain\Training\EventTransformer;

use Respect\Validation\Validator as v;

use Core\Validators\ValidationException;
use Core\Validators\InputValidator;
use Core\Validators\EntityExistValidator;

use Core\Responses\UnprocessableEntityResponse;
use Core\Responses\ResourceResponse;

class EventCreateAction
{
    private $container;

    private $inputValidator;

    private $eventValidator;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        $this->inputValidator = (new InputValidator(
            [
                'data.attributes.name' => v::length(1, 255),
                'data.attributes.start_date' => v::date('Y-m-d'),
                'data.attributes.start_time' => v::date('H:i'),
                'data.attributes.end_time' => v::date('H:i'),
                'data.attributes.time_zone' => v::length(1, 255),
                'data.attributes.active' => [ v::boolType(), true ],
                'data.attributes.location' => [ v::length(1, 255), true ]
            ]
        ));
        $this->eventValidator = new \REST\Trainings\EventValidator();
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        try {
            if (is_array($data)) {
                $events = [];
                foreach ($data as $item) {
                    $events[] = $this->createEvent($request, $item);
                }
                $response = (new ResourceResponse(
                    EventTransformer::createForCollection($events)
                ))($response)->withStatus(201);
            } else {
                $event = $this->createEvent($request, $data);
                $route = $request->getAttribute('route');
                if (! empty($route)) {
                    $route->setArgument('id', $event->id);
                }
                $response = (new ResourceResponse(
                    EventTransformer::createForItem($event)
                ))($response)->withStatus(201);
            }
        } catch (ValidationException $ve) {
            $response = (new UnprocessableEntityResponse(
                $ve->getErrors()
            ))($response);
        }

        return $response;
    }

    private function createEvent($request, $data)
    {
        $this->inputValidator->validate($data);

        $table = EventsTable::getTableFromRegistry();

        $season = (new EntityExistValidator(
            'data.relationships.season',
            $table->Season,
            false
        ))->validate($data);

        $def = (new EntityExistValidator(
            'data.relationships.definition',
            $table->TrainingDefinition,
            false
        ))->validate($data);

        $attributes = \JmesPath\search('data.attributes', $data);

        $event = $table->newEntity();
        $event->name = $attributes['name'];
        $event->description = $attributes['description'];
        $event->start_date = $attributes['start_date'];
        $event->start_time = $attributes['start_time'];
        $event->end_time = $attributes['end_time'];
        $event->time_zone = $attributes['time_zone'];
        $event->active = $attributes['active'] ?? true;
        $event->remark = $attributes['remark'];
        $event->season = $season;
        $event->training_definition = $def;
        $event->user = $request->getAttribute('clubman.user');

        $this->eventValidator->validate($event);

        $table->save($event);

        return $event;
    }
}
