<?php

namespace REST\Teams\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Team\TeamTypesTable;
use Domain\Team\TeamTypeTransformer;

use Cake\Datasource\Exception\RecordNotFoundException;

class TypeReadAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        try {
            return (new \Core\ResourceResponse(
                TeamTypeTransformer::createForItem(
                    TeamTypesTable::getTableFromRegistry()->get($args['id'])
                )
            ))($response);
        } catch (RecordNotFoundException $rnfe) {
            return $response->withStatus(404, _("Teamtype doesn't exist"));
        }
    }
}
