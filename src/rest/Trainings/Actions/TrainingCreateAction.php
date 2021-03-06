<?php

namespace REST\Trainings\Actions;

use Core\Validators\EntityExistValidator;
use Core\Validators\InputValidator;
use Core\Validators\ValidationException;
use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cake\ORM\Entity;

use Domain\Event\EventsTable;
use Domain\Content\ContentsTable;
use Domain\Training\TrainingsTable;
use Domain\Training\TrainingTransformer;

use Respect\Validation\Validator as v;

use REST\Trainings\TrainingValidator;

use Kwai\Core\Infrastructure\Presentation\Responses\UnprocessableEntityResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;

class TrainingCreateAction
{
    private $container;

    private $eventInputValidator;


    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->eventInputValidator = new InputValidator([
            'data.attributes.event.start_date' => v::date('Y-m-d H:i:s'),
            'data.attributes.event.end_date' => v::date('Y-m-d H:i:s'),
            'data.attributes.event.time_zone' => v::length(1, 255),
            'data.attributes.event.active' => [ v::boolType(), true ],
            'data.attributes.event.location' => [ v::length(1, 255), true ]
        ]);
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        try {
            if ($this->isAssoc($data['data'])) {
                $training = $this->createEvent($request, $data);
                $route = $request->getAttribute('route');
                if (! empty($route)) {
                    $route->setArgument('id', $training->id);
                }
                $response = (new ResourceResponse(
                    TrainingTransformer::createForItem($training)
                ))($response)->withStatus(201);
            } else {
                $trainings = [];
                foreach ($data['data'] as $item) {
                    $trainings[] = $this->createEvent($request, [
                        'data' => $item
                    ]);
                }
                $response = (new ResourceResponse(
                    TrainingTransformer::createForCollection($trainings)
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
        $trainingsTable = TrainingsTable::getTableFromRegistry();

        $season = (new EntityExistValidator(
            'data.relationships.season',
            $trainingsTable->Season,
            false
        ))->validate($data);

        $def = (new EntityExistValidator(
            'data.relationships.definition',
            $trainingsTable->TrainingDefinition,
            false
        ))->validate($data);

        $coaches = (new EntityExistValidator(
            'data.relationships.coaches',
            $trainingsTable->Coaches,
            false
        ))->validate($data);

        $teams = (new EntityExistValidator(
            'data.relationships.teams',
            $trainingsTable->Teams,
            false
        ))->validate($data);

        $this->eventInputValidator->validate($data);

        $attributes = \JmesPath\search('data.attributes', $data);

        $training = $trainingsTable->newEntity();
        $event = EventsTable::getTableFromRegistry()->newEntity();
        $training->event = $event;
        $content = ContentsTable::getTableFromRegistry()->newEntity();
        $event->contents = [ $content ];
        $content->title = $attributes['event']['contents'][0]['title'];
        $content->locale = $attributes['event']['contents'][0]['locale'] ?? 'nl';
        $content->format = $attributes['event']['contents'][0]['format'] ?? 'md';
        $content->summary = $attributes['event']['contents'][0]['summary'];
        $content->user_id = $request->getAttribute('kwai.user')->id();
        $event->start_date = $attributes['event']['start_date'];
        $event->end_date = $attributes['event']['end_date'];
        $event->time_zone = $attributes['event']['time_zone'];
        $event->active = $attributes['event']['active'] ?? true;
        $event->remark = $attributes['event']['remark'] ?? null;
        $event->user_id = $request->getAttribute('kwai.user')->id();
        $training->season = $season;
        $training->definition = $def;
        $training->teams = $teams;

        (new TrainingValidator())->validate($training);

        $trainingsTable->save($training);

        foreach ($coaches as $coach) {
            $coach->_joinData = new Entity([
                'coach_type' => 0,
                'present' => false,
                'user_id' => $request->getAttribute('kwai.user')->id()
            ], [
                'markNew' => true
            ]);
            $trainingsTable->Coaches->link($training, [$coach]);
        }

        $training = $trainingsTable->get($training->id, [
            'contain' => [
                'TrainingDefinition',
                'Season',
                'Coaches',
                'Coaches.Member',
                'Coaches.Member.Person',
                'Members',
                'Members.Person',
                'Teams',
                'Event',
                'Event.Contents'
            ]
        ]);

        return $training;
    }

    private function isAssoc($array)
    {
        foreach ($array as $key => $value) {
            if ($key !== (int) $key) {
                return true;
            }
        }
        return false;
    }
}
