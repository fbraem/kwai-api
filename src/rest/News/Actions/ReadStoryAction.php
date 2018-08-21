<?php

namespace REST\News\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\News\NewsStoryTransformer;
use Domain\News\NewsStoriesTable;

use Cake\Datasource\Exception\RecordNotFoundException;

class ReadStoryAction extends \Core\Action
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

            return $this->createJSONResponse($response, $resource);
        } catch (RecordNotFoundException $rnfe) {
            $response = $response->withStatus(404, _("Story doesn't exist"));
        }

        return $response;
    }
}
