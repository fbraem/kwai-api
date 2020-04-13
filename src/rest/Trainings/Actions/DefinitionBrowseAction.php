<?php

namespace REST\Trainings\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Training\DefinitionsTable;
use Domain\Training\DefinitionTransformer;

use Kwai\Core\Infrastructure\Responses\ResourceResponse;

class DefinitionBrowseAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $table = DefinitionsTable::getTableFromRegistry();
        $query = $table->find();
        $query->contain([
            'Season',
            'Team'
        ]);

        return (new ResourceResponse(
            DefinitionTransformer::createForCollection(
                $query->all()
            )
        ))($response);
    }
}
