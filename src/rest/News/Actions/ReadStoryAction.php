<?php

namespace REST\News\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;

use Domain\News\NewsStoryTransformer;
use Domain\News\NewsStoriesTable;

use Cake\Datasource\Exception\RecordNotFoundException;

class ReadStoryAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        try {
            $story = NewsStoriesTable::getTableFromRegistry()->get($args['id'], [
                'contain' => ['Contents', 'Category', 'Contents.User']
            ]);

            $filesystem = $this->container->get('filesystem');
            $resource = NewsStoryTransformer::createForItem($story, $filesystem);

            $fractal = new Manager();
            $fractal->setSerializer(new JsonApiSerializer(/*$this->baseURL*/));
            $data = $fractal->createData($resource)->toJson();

            $response = $response
                ->withHeader('content-type', 'application/vnd.api+json')
                ->getBody()
                ->write($data);
        } catch (RecordNotFoundException $rnfe) {
            $response = $response->withStatus(404, _("Story doesn't exist"));
        }

        return $response;
    }
}
