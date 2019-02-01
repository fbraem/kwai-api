<?php

namespace REST\Events\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Event\EventsTable;
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
        $query->contain(['Category', 'User', 'Contents']);

        $parameters = $request->getAttribute('parameters');
        if (isset($parameters['filter']['year'])) {
            $query->where([
                'YEAR(Events.start_date)' => $parameters['filter']['year']
            ]);
            if (isset($parameters['filter']['month'])) {
                $query->where([
                    'MONTH(Events.start_date)' => $parameters['filter']['month']
                ]);
            }
        }

        return (new ResourceResponse(
            EventTransformer::createForCollection(
                $query->all()
            )
        ))($response);
    }
}
