<?php

namespace REST\Trainings\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Training\DefinitionsTable;
use Domain\Training\DefinitionTransformer;

class DefinitionBrowseAction extends \Core\Action
{
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $table = DefinitionsTable::getTableFromRegistry();
        $query = $table->find();
        $query->contain(['Season']);

        return (new \Core\ResourceResponse(
            DefinitionTransformer::createForCollection(
                $query->all()
            )
        ))($response);
    }
}
