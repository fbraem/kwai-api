<?php

namespace REST\Pages\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;
use Core\Responders\NotFoundResponder;

use League\Fractal;

class ReadAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $id = $request->getAttribute('route.id');

        try {
            $page = \Domain\Page\PagesTable::getTableFromRegistry()->get($id, [
                'contain' => ['Contents', 'Category', 'Contents.User']
            ]);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
            return (
                new NotFoundResponder(
                    new Responder(),
                    _("Page doesn't exist.")
                ))->respond();
        }

        $filesystem = $request->getAttribute('clubman.container')['filesystem'];
        $payload->setOutput(\Domain\Page\PageTransformer::createForItem($page, $filesystem));
        return (
            new JSONResponder(
                new Responder(),
                $payload
            ))->respond();
    }
}
