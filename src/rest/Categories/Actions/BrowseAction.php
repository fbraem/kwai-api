<?php

namespace REST\Categories\Actions;

use Interop\Container\ContainerInterface;

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
        return (new \Core\Responses\ResourceResponse(
            CategoryTransformer::createForCollection(
                CategoriesTable::getTableFromRegistry()->find()->all(),
                $this->container->get('filesystem')
            )
        ))($response);
    }
}
