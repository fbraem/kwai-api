<?php

namespace REST\Trainings\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cake\ORM\Entity;

use Domain\Training\TrainingsTable;
use Domain\Training\TrainingTransformer;

use Cake\Datasource\Exception\RecordNotFoundException;

use Respect\Validation\Validator as v;

use Kwai\Core\Infrastructure\Validators\ValidationException;
use Kwai\Core\Infrastructure\Validators\InputValidator;
use Kwai\Core\Infrastructure\Validators\EntityExistValidator;

use Kwai\Core\Infrastructure\Presentation\Responses\UnprocessableEntityResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;

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
                    'Coaches',
                    'Teams',
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

            $coaches = (new EntityExistValidator(
                'data.relationships.coaches',
                $table->Coaches,
                false
            ))->validate($data);

            $teamData = \JmesPath\search('data.relationships.teams', $data);
            if (isset($teamData)) {
                $teams = (new EntityExistValidator(
                    'data.relationships.teams',
                    $table->Teams,
                    false
                ))->validate($data);
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
            if (isset($attributes['event']['cancelled'])) {
                $training->event->cancelled = $attributes['event']['cancelled'];
            }
            if (isset($attributes['event']['remark'])) {
                $training->event->remark = $attributes['event']['remark'];
            }

            $training->event->setDirty('contents', true);
            $training->setDirty('event', true);
            $training->event->user = $request->getAttribute('clubman.user');

            (new \REST\Trainings\TrainingValidator())->validate($training);

            $table->save($training, [
                'associated' => [
                    'Event.Contents'
                ]
            ]);

            // Update teams
            if ($teams) {
                // When a team is not passed to this function, it must be deleted
                $lookup = array_column($teams, null, 'id');
                $toDelete = [];
                foreach ($training->teams as $team) {
                    if (!$lookup[$team->id]) {
                        $toDelete[] = $team;
                    }
                }
                if (count($toDelete) > 0) {
                    $table->Teams->unlink($training, $toDelete);
                }

                // When a team is passed to this function and it's not in the
                // table, it must be insert
                $lookup = array_column($training->teams, null, 'id');
                $toInsert = [];
                foreach ($teams as $team) {
                    if (!$lookup[$team->id]) {
                        $toInsert[] = $team;
                    }
                }
                if (count($toInsert) > 0) {
                    $table->Teams->link($training, $toInsert);
                }
            }

            // Update coaches
            if ($coaches) {
                // When a coach is not passed to this function, it must be deleted
                $lookup = array_column($coaches, null, 'id');
                $toDelete = [];
                foreach ($training->coaches as $coach) {
                    if (!isset($lookup[$coach->id])) {
                        $toDelete[] = $coach;
                    }
                }
                if (count($toDelete) > 0) {
                    $table->Coaches->unlink($training, $toDelete);
                }

                // When a coach is passed to this function and it's not in the
                // table, it must be insert
                $lookup = array_column($training->coaches, null, 'id');
                $toInsert = [];
                foreach ($coaches as $coach) {
                    if (!isset($lookup[$coach->id])) {
                        $coach->_joinData = new Entity([
                            'coach_type' => 0,
                            'present' => false,
                            'user' => $request->getAttribute('clubman.user')
                        ], [
                            'markNew' => true
                        ]);
                        $toInsert[] = $coach;
                    }
                }
                if (count($toInsert) > 0) {
                    $table->Coaches->link($training, $toInsert);
                }
            }

            $route = $request->getAttribute('route');
            if (! empty($route)) {
                $route->setArgument('id', $training->id);
            }

            $response = (new ResourceResponse(
                TrainingTransformer::createForItem($training)
            ))($response);
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
