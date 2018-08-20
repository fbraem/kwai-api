<?php

namespace REST\Pages\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;

use Domain\Page\PageTransformer;
use Domain\Page\PagesTable;

use Cake\Datasource\Exception\RecordNotFoundException;

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
            $page = PagesTable::getTableFromRegistry()->get($args['id'], [
                'contain' => ['Contents', 'Category', 'Contents.User']
            ]);
        } catch (RecordNotFoundException $rnfe) {
            $response = $response.withStatus(404, _("Story doesn't exist"));
        }

        $filesystem = $this->container->get('filesystem');
        $resource = PageTransformer::createForItem($page, $filesystem);

        $fractal = new Manager();
        $fractal->setSerializer(new JsonApiSerializer(/*$this->baseURL*/));
        $data = $fractal->createData($resource)->toJson();

        return $response
            ->withHeader('content-type', 'application/vnd.api+json')
            ->getBody()
            ->write($data);
    }
}
