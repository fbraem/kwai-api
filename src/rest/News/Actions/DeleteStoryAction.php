<?php

namespace REST\News\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\News\NewsStoriesTable;
use \Domain\Content\ContentsTable;

use Cake\Datasource\Exception\RecordNotFoundException;

use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\OkResponse;

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
            $contentTable = ContentsTable::getTableFromRegistry();
            foreach ($story->contents as $content) {
                $contentTable->delete($content);
            }
            $storiesTable->delete($story);

            $filesystem = $this->container->get('filesystem');
            $folder = 'images/news/' . $args['id'];
            $filesystem->deleteDir($folder);

            $response = (new OKResponse())($response);
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("Story doesn't exist")))($response);
        }

        return $response;
    }
}
