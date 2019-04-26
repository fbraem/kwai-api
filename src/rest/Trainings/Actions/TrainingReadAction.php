<?php

namespace REST\Trainings\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cake\Datasource\Exception\RecordNotFoundException;

use Domain\Training\TrainingsTable;
use Domain\Training\TrainingTransformer;

use Core\Responses\ResourceResponse;
use Core\Responses\NotFoundResponse;

class TrainingReadAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $table = TrainingsTable::getTableFromRegistry();
        try {
            $training = $table->get($args['id'], [
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

            $response = (new ResourceResponse(
                TrainingTransformer::createForItem(
                    $training
                )
            ))($response);
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("Training doesn't exist")))($response);
        }
        return $response;
    }
}
