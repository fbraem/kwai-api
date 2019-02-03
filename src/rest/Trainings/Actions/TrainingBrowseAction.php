<?php

namespace REST\Trainings\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Training\TrainingsTable;
use Domain\Training\TrainingTransformer;

use Core\Responses\ResourceResponse;

class TrainingBrowseAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $table = TrainingsTable::getTableFromRegistry();
        $query = $table->find();
        $query->contain([
            'Season',
            'TrainingDefinition',
            'TrainingCoaches',
            'Event',
            'Event.Contents'
        ]);

        $parameters = $request->getAttribute('parameters');
        if (isset($parameters['filter']['year'])) {
            $query->where([
                'YEAR(Event.start_date)' => $parameters['filter']['year']
            ]);
            if (isset($parameters['filter']['month'])) {
                $query->where([
                    'MONTH(Event.start_date)' => $parameters['filter']['month']
                ]);
            }
        }

        return (new ResourceResponse(
            TrainingTransformer::createForCollection(
                $query->all()
            )
        ))($response);
    }
}
