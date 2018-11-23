<?php

namespace REST\Trainings\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Training\CoachesTable;
use Domain\Training\CoachTransformer;

use Core\Responses\ResourceResponse;

class CoachBrowseAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $table = CoachesTable::getTableFromRegistry();
        $query = $table->find();
        $query->contain(['Member', 'Member.Person']);

        return (new ResourceResponse(
            CoachTransformer::createForCollection(
                $query->all()
            )
        ))($response);
    }
}
