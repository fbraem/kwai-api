<?php

namespace REST\Trainings\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Training\EventsTable;
use Domain\Training\EventCoachesTable;
use Domain\Training\EventTransformer;

use Respect\Validation\Validator as v;

use Core\Validators\ValidationException;
use Core\Validators\InputValidator;
use Core\Validators\EntityExistValidator;

use Core\Responses\UnprocessableEntityResponse;
use Core\Responses\ResourceResponse;

class EventCoachCreateAction
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
                    'data.attributes.coach_type' => v::digits(),
                    'data.attributes.present' => [ v::boolType(), true ]
                ]
            ))->validate($data);

            $eventsTable = EventsTable::getTableFromRegistry();
            $eventsTable->get(
                $args['id'],
                [ 'contain' => [ 'Coaches']]
            );

            $eventCoachesTable = EventCoachesTable::getTableFromRegistry();

            $coach = (new EntityExistValidator(
                'data.relationships.coaches',
                $eventCoachesTable->Coach,
                true
            ))->validate($data);

            $attributes = \JmesPath\search('data.attributes', $data);

            $eventCoach = $eventCoachesTable->newEntity();
            $eventCoach->remark = $attributes['remark'];
            $eventCoach->coach_type = $attributes['coach_type'] ?? 0;
            $eventCoach->presence = $attributes['presence'] ?? false;
            $eventCoach->user = $request->getAttribute('clubman.user');
            $eventCoachesTable->save($eventCoach);

            $route = $request->getAttribute('route');
            if (! empty($route)) {
                $route->setArgument('id', $event->id);
            }

            $event->coaches[] = $eventCoach;
            $eventsTable->save($event);

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
