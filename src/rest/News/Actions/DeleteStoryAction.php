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

class DeleteStoryAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $storiesTable = NewsStoriesTable::getTableFromRegistry();
        try {
            $story = $storiesTable->get($args['id'], [
                'contain' => ['Contents']
            ]);
            $contentTable = \Domain\Content\ContentsTable::getTableFromRegistry();
            foreach ($story->contents as $content) {
                $contentTable->delete($content);
            }
            $storiesTable->delete($story);

            $filesystem = $this->container->get('filesystem');
            $folder = 'images/news/' . $args['id'];
            $filesystem->deleteDir($folder);

            $response = $response->withStatus(200);
        } catch (RecordNotFoundException $rnfe) {
            $response = $response->withStatus(404, _("Story doesn't exist"));
        }

        return $response;
    }
}
