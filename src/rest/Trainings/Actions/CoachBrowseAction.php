<?php

namespace REST\Trainings\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Training\CoachesTable;
use Domain\Training\CoachTransformer;

use Kwai\Core\Infrastructure\Responses\ResourceResponse;

class CoachBrowseAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $parameters = $request->getAttribute('parameters');

        $table = CoachesTable::getTableFromRegistry();
        $query = $table->find();
        $query->contain([
            'Member',
            'Member.Person'
        ]);

        if (in_array('name', $parameters['sort'])) {
            $query->order([
                'Person.lastname',
                'Person.firstname'
            ]);
        }

        return (new ResourceResponse(
            CoachTransformer::createForCollection(
                $query->all()
            )
        ))($response);
    }
}
