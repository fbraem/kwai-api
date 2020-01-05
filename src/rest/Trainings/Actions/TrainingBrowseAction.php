<?php

namespace REST\Trainings\Actions;

use Psr\Container\ContainerInterface;

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
            'Coaches',
            'Coaches.Member',
            'Coaches.Member.Person',
            'Teams',
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

        if (isset($parameters['filter']['coach'])) {
            $query->matching('Coaches', function ($q) use ($parameters) {
                return $q->where([
                    'coach_id' => $parameters['filter']['coach']
                ]);
            });
        }
        $query->where(['Event.active' => 1]);
        $query->order(['Event.start_date']);

        $count = $query->count();

        $limit = $parameters['page']['limit'] ?? 10;
        $offset = $parameters['page']['offset'] ?? 0;

        //$query->limit($limit);
        //$query->offset($offset);

        $resource = TrainingTransformer::createForCollection(
            $query->all()
        );
        $resource->setMeta([
            'limit' => intval($limit),
            'offset' => intval($offset),
            'count' => $count
        ]);

        return (new ResourceResponse($resource))($response);
    }
}
