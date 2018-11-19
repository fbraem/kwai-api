<?php

namespace REST\Persons\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Person\PersonsTable;
use Domain\Person\PersonTransformer;

use Core\Responses\ResourceResponse;

class BrowseAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        return (new ResourceResponse(
            PersonTransformer::createForCollection(
                PersonsTable::getTableFromRegistry()
                    ->find()
                    ->contain(['Nationality', 'Contact', 'Contact.Country'])
                    ->all()
            )
        ))($response);
    }
}
