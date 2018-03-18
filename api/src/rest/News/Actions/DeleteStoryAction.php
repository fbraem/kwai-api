<?php

namespace REST\News\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\HTTPCodeResponder;
use Core\Responders\NotFoundResponder;

class DeleteStoryAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $id = $request->getAttribute('route.id');

        $storiesTable = \Domain\News\NewsStoriesTable::getTableFromRegistry();
        try {
            $story = $storiesTable->get($id, [
                'contain' => ['Contents']
            ]);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
            return (
                new NotFoundResponder(
                    new Responder(),
                    _("Story doesn't exist.")
                ))->respond();
        }

        $contentTable = \Domain\Content\ContentsTable::getTableFromRegistry();
        foreach ($story->contents as $content) {
            $contentTable->delete($content);
        }
        $storiesTable->delete($story);

        $filesystem = $request->getAttribute('clubman.container')['filesystem'];
        $folder = 'images/news/' . $id;
        $filesystem->deleteDir($folder);

        return (
            new HTTPCodeResponder(
                new Responder(),
                200
            ))->respond();
    }
}
