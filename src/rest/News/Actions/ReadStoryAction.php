<?php

namespace REST\News\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\News\NewsStoryTransformer;
use Domain\News\NewsStoriesTable;

use Cake\Datasource\Exception\RecordNotFoundException;

use Core\Responses\NotFoundResponse;
use Core\Responses\ResourceResponse;

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

            $response = (new ResourceResponse(
                NewsStoryTransformer::createForItem($story, $filesystem)
            ))($response);
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("Story doesn't exist")))($response);
        }

        return $response;
    }
}
