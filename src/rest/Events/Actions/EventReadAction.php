<?php

namespace REST\Trainings\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cake\Datasource\Exception\RecordNotFoundException;

use Domain\Training\EventsTable;
use Domain\Training\EventTransformer;

use Core\Responses\ResourceResponse;
use Core\Responses\NotFoundResponse;

class EventReadAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $table = EventsTable::getTableFromRegistry();
        try {
            $coach = $table->get($args['id'], [
                'contain' => ['TrainingDefinition', 'Season', 'TrainingCoaches' ]
            ]);

            $response = (new ResourceResponse(
                EventTransformer::createForItem(
                    $coach
                )
            ))($response);
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("Event doesn't exist")))($response);
        }
        return $response;
    }
}
