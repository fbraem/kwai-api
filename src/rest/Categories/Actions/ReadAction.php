<?php

namespace REST\Categories\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Cake\Datasource\Exception\RecordNotFoundException;

use Domain\Category\CategoriesTable;
use Domain\Category\CategoryTransformer;

class ReadAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        try {
            $response = (new \Core\ResourceResponse(
                CategoryTransformer::createForItem(
                    CategoriesTable::getTableFromRegistry()->get($args['id'])
                )
            ))($response);
        } catch (RecordNotFoundException $rnfe) {
            $response = $response->withStatus(404, _("Category doesn't exist"));
        }

        return $response;
    }
}
