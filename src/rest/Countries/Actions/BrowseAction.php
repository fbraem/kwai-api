<?php

namespace REST\Countries\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Person\CountryTransformer;
use Domain\Person\CountriesTable;

class BrowseAction extends \Core\Action
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $countries = CountriesTable::getTableFromRegistry()->find()->all();
        $resource = CountryTransformer::createForCollection($countries);

        return $this->createJSONResponse($response, $resource);
    }
}
