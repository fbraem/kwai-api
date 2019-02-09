<?php

namespace REST\Trainings\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Training\TrainingsTable;
use Domain\Training\TrainingTransformer;

use Cake\Datasource\Exception\RecordNotFoundException;

use Respect\Validation\Validator as v;

use Core\Validators\ValidationException;
use Core\Validators\InputValidator;
use Core\Validators\EntityExistValidator;

use Core\Responses\UnprocessableEntityResponse;
use Core\Responses\ResourceResponse;
use Core\Responses\NotFoundResponse;

class TrainingUpdateAction
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
            $table = TrainingsTable::getTableFromRegistry();

            $training = $table->get($args['id'], [
                'contain' => [
                    'TrainingDefinition',
                    'Season',
                    'Event',
                    'Event.Contents'
                ]
            ]);

            (new InputValidator(
                [
                    'data.attributes.event.start_date' => v::date('Y-m-d H:i:s'),
                    'data.attributes.event.end_date' => v::date('Y-m-d H:i:s'),
                    'data.attributes.event.time_zone' => v::length(1, 255),
                    'data.attributes.event.active' => v::boolType(),
                    'data.attributes.event.location' => v::length(1, 255)
                ],
                true
            ))->validate($data);

            $seasonData = \JmesPath\search('data.relationships.season.data', $data);
            if (isset($seasonData)) {
                $season = (new EntityExistValidator(
                    'data.relationships.season',
                    $table->Season,
                    false
                ))->validate($data);
                if ($season) {
                    $training->season = $season;
                } else {
                    $training->season_id = null;
                    $training->season = null;
                }
            }

            $defData = \JmesPath\search('data.relationships.definition.data', $data);
            if (isset($defData)) {
                $def = (new EntityExistValidator(
                    'data.relationships.definition',
                    $table->TrainingDefinition,
                    false
                ))->validate($data);
                if ($def) {
                    $training->definition = $def;
                } else {
                    $training->training_definition_id = null;
                    $training->definition = null;
                }
            }

            $attributes = \JmesPath\search('data.attributes', $data);

            if (isset($attributes['event']['contents'][0]['title'])) {
                $training->event->contents[0]->title = $attributes['event']['contents'][0]['title'];
            }
            if (isset($attributes['event']['contents'][0]['summary'])) {
                $training->event->contents[0]->summary = $attributes['event']['contents'][0]['summary'];
            }
            $training->event->contents[0]->user = $request->getAttribute('clubman.user');

            if (isset($attributes['event']['start_date'])) {
                $training->event->start_date = $attributes['event']['start_date'];
            }
            if (isset($attributes['event']['end_date'])) {
                $training->event->end_date = $attributes['event']['end_date'];
            }
            if (isset($attributes['event']['time_zone'])) {
                $training->event->time_zone = $attributes['event']['time_zone'];
            }
            if (isset($attributes['event']['active'])) {
                $training->event->active = $attributes['event']['active'];
            }
            if (isset($attributes['event']['remark'])) {
                $training->event->remark = $attributes['event']['remark'];
            }

            $training->event->dirty('contents', true);
            $training->dirty('event', true);
            $training->event->user = $request->getAttribute('clubman.user');

            (new \REST\Trainings\TrainingValidator())->validate($training);

            $table->save($training, [
                'associated' => [
                    'Event.Contents'
                ]
            ]);

            $route = $request->getAttribute('route');
            if (! empty($route)) {
                $route->setArgument('id', $training->id);
            }

            $response = (new ResourceResponse(
                TrainingTransformer::createForItem($training)
            ))($response)->withStatus(201);
        } catch (ValidationException $ve) {
            $response = (new UnprocessableEntityResponse(
                $ve->getErrors()
            ))($response);
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("Training doesn't exist")))($response);
        }

        return $response;
    }
}
