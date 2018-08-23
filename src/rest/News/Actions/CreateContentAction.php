<?php

namespace REST\News\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\News\NewsStoryTransformer;
use Domain\News\NewsStoriesTable;

use REST\Contents\ContentValidator;
use Domain\Content\ContentsTable;

use Cake\Datasource\Exception\RecordNotFoundException;

class CreateContentAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        $storiesTable = NewsStoriesTable::getTableFromRegistry();
        try {
            $story = $storiesTable->get($args['id'], [
                'contain' => ['Contents', 'Category', 'Contents.User']
            ]);
        } catch (RecordNotFoundException $rnfe) {
            return $response->withStatus(404, _("Story doesn't exist"));
        }

        $validator = new ContentValidator();
        if (! $validator->validate($data)) {
            return $validator->unprocessableEntityResponse($response);
        }

        $contentsTable = ContentsTable::getTableFromRegistry();
        $attributes = \JmesPath\search('data.attributes', $data);

        $content = $contentsTable->newEntity();
        $content->locale = 'nl';
        $content->format = 'md';
        $content->title = $attributes['title'];
        $content->summary = $attributes['summary'];
        $content->content = $attributes['content'];
        $content->user = $request->getAttribute('clubman.user');

        $story->contents = [ $content ];
        $storiesTable->save($story);

        $filesystem = $this->container->get('filesystem');

        return (new \Core\ResourceResponse(
            NewsStoryTransformer::createForItem($story, $filesystem)
        ))($response)->withStatus(201);
    }
}
