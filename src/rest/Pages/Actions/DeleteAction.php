<?php

namespace REST\Pages\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\HTTPCodeResponder;
use Core\Responders\NotFoundResponder;

class DeleteAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $id = $request->getAttribute('route.id');

        $pageTable = \Domain\Page\PagesTable::getTableFromRegistry();
        try {
            $page = $pageTable->get($id, [
                'contain' => ['Contents']
            ]);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
            return (
                new NotFoundResponder(
                    new Responder(),
                    _("Page doesn't exist.")
                ))->respond();
        }

        $contentTable = \Domain\Content\ContentsTable::getTableFromRegistry();
        foreach ($page->contents as $content) {
            $contentTable->delete($content);
        }
        $pageTable->delete($page);

        $filesystem = $request->getAttribute('clubman.container')['filesystem'];
        $folder = 'images/pages/' . $id;
        $filesystem->deleteDir($folder);

        return (
            new HTTPCodeResponder(
                new Responder(),
                200
            ))->respond();
    }
}
