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
        $db = $request->getAttribute('clubman.container')['db'];

        $pagesTable = new \Domain\Page\PagesTable($db);
        try {
            $page = $pagesTable->whereId($id)->findOne();
        } catch (\Domain\NotFoundException $nfe) {
            return (new NotFoundResponder(new Responder(), _("Page doesn't exist.")))->respond();
        }

        $page->delete();

        $filesystem = $request->getAttribute('clubman.container')['filesystem'];
        $folder = 'images/pages/' . $id;
        $filesystem->deleteDir($folder);

        return (new HTTPCodeResponder(new Responder(), 200))->respond();
    }
}
