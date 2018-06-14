<?php

namespace REST\News\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\JSONErrorResponder;
use Core\Responders\HTTPCodeResponder;
use Core\Responders\NotFoundResponder;

class UpdateContentAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $id = $request->getAttribute('route.id');

        $storiesTable = \Domain\News\NewsStoriesTable::getTableFromRegistry();
        try {
            $story = $storiesTable->get($id, [
                'contain' => ['Contents', 'Contents.User']
            ]);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
            return (
                new NotFoundResponder(
                    new Responder(),
                    _("Story doesn't exist.")
                ))->respond();
        }

        $data = $payload->getInput();

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

        $filesystem = $request->getAttribute('clubman.container')['filesystem'];
        $payload->setOutput(\Domain\News\NewsStoryTransformer::createForItem($story, $filesystem));

        return (
            new JSONResponder(
                new HTTPCodeResponder(
                    new Responder(),
                    201
                ),
                $payload
            )
        )->respond();
    }
}
