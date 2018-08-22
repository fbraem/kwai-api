<?php

namespace REST\Categories\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Category\CategoriesTable;
use Domain\Category\CategoryTransformer;

class BrowseCategoryAction extends \Core\Action
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $categories = CategoriesTable::getTableFromRegistry()->find()->all();
        $resource = CategoryTransformer::createForCollection($categories);

        return $this->createJSONResponse($response, $resource);
    }
}
