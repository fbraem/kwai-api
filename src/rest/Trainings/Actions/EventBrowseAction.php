<?php

namespace REST\Trainings\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Training\EventsTable;
use Domain\Training\EventTransformer;

use Core\Responses\ResourceResponse;

class EventBrowseAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $table = EventsTable::getTableFromRegistry();
        $query = $table->find();
        $query->contain(['Season', 'TrainingDefinition', 'TrainingCoaches']);

        return (new ResourceResponse(
            EventTransformer::createForCollection(
                $query->all()
            )
        ))($response);
    }
}
