<?php

namespace REST\Categories\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Category\CategoriesTable;
use Domain\Category\CategoryTransformer;

class BrowseAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $parameters = $request->getAttribute('parameters');

        $query = CategoriesTable::getTableFromRegistry()->find();

        if (isset($parameters['filter']['app'])) {
            $query->where(['app' => $parameters['filter']['app']]);
        }

        return (new \Core\Responses\ResourceResponse(
            CategoryTransformer::createForCollection(
                $query->all(),
                $this->container->get('filesystem')
            )
        ))($response);
    }
}
