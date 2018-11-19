<?php

namespace REST\Countries\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Person\CountryTransformer;
use Domain\Person\CountriesTable;

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
            CountryTransformer::createForCollection(
                CountriesTable::getTableFromRegistry()->find()->all()
            )
        ))($response);
    }
}
