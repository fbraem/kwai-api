<?php

namespace REST\Pages\Actions;

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

        $pagesTable = \Domain\Page\PagesTable::getTableFromRegistry();
        try {
            $page = $pagesTable->get($id, [
                'contain' => ['Category', 'Contents', 'Contents.User']
            ]);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
            return (
                new NotFoundResponder(
                    new Responder(),
                    _("Page doesn't exist.")
                ))->respond();
        }

        $data = $payload->getInput();

        $attributes = \JmesPath\search('data.attributes', $data);

        //TODO: for now we only support one content.
        // In the future we will support multi lingual
        $content = $page->contents[0];
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
            $page->contents = [ $content ];
        }

        $pagesTable->save($page);

        $filesystem = $request->getAttribute('clubman.container')['filesystem'];
        $payload->setOutput(\Domain\Page\PageTransformer::createForItem($page, $filesystem));

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
