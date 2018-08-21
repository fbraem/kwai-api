<?php

namespace REST\News\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\News\NewsStoryTransformer;
use Domain\News\NewsStoriesTable;
use REST\Contents\ContentValidator;

use Cake\Datasource\Exception\RecordNotFoundException;

class UpdateContentAction extends \Core\Action
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
                'contain' => ['Category', 'Contents', 'Contents.User']
            ]);
        } catch (RecordNotFoundException $rnfe) {
            $response->withStatus(404, _("Story doesn't exist."));
        }

        $data = $request->getParsedBody();
        $validator = new ContentValidator();
        if (! $validator->validate($data)) {
            return $validator->unprocessableEntityResponse($response);
        }

        $attributes = \JmesPath\search('data.attributes', $data);

        //TODO: for now we only support one content.
        // In the future we will support multi lingual
        $content = $story->contents[0];
        if (isset($attributes['title'])) {
            $content->title = $attributes['title'];
        }
        if (isset($attributes['summary'])) {
            $content->summary = $attributes['summary'];
        }
        if (isset($attributes['content'])) {
            $content->content = $attributes['content'];
        }
        if ($content->isDirty()) {
            $content->user = $request->getAttribute('clubman.user');
            $story->contents = [ $content ];
        }

        $storiesTable->save($story);

        $filesystem = $this->container->get('filesystem');
        $resource = NewsStoryTransformer::createForItem($story, $filesystem);

        return $this->createJSONResponse($response, $resource);
    }
}
