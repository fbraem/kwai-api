<?php

namespace REST\News\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\JSONErrorResponder;
use Core\Responders\HTTPCodeResponder;

class CreateContentAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $data = $payload->getInput();

        $id = $request->getAttribute('route.id');

        $storiesTable = \Domain\News\NewsStoriesTable::getTableFromRegistry();
        try {
            $story = $storiesTable->get($id, [
                'contain' => ['Contents', 'Category', 'Contents.User']
            ]);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
            return (
                new NotFoundResponder(
                    new Responder(),
                    _("Story doesn't exist.")
                ))->respond();
        }

        $validator = new \REST\Contents\ContentValidator();
        $errors = $validator->validate($data);
        if (count($errors) > 0) {
            return (
                new JSONErrorResponder(
                    new HTTPCodeResponder(
                        new Responder(),
                        422
                    ),
                    $errors
                )
            )->respond();
        }


        $contentsTable = \Domain\Content\ContentsTable::getTableFromRegistry();
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
