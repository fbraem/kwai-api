<?php

namespace REST\Categories\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;

use Cake\Datasource\Exception\RecordNotFoundException;

use Domain\Category\CategoriesTable;
use Domain\Category\CategoryTransformer;

class ReadCategoryAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        try {
            $category = CategoriesTable::getTableFromRegistry()->get($args['id']);
            $resource = CategoryTransformer::createForItem($category);

            $fractal = new Manager();
            $fractal->setSerializer(new JsonApiSerializer(/*$this->baseURL*/));
            $data = $fractal->createData($resource)->toJson();

            $response = $response
                ->withHeader('content-type', 'application/vnd.api+json')
                ->getBody()
                ->write($data);
        } catch (RecordNotFoundException $rnfe) {
            $response = $response->withStatus(404, _("Category doesn't exist"));
        }

        return $response;
    }
}
