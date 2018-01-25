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
        $db = $request->getAttribute('clubman.container')['db'];

        $pages = new \Domain\Page\PagesTable($db);
        try {
            $page = $pages->whereId($id)->findOne();
        } catch (\Domain\NotFoundException $nfe) {
            return (new NotFoundResponder(new Responder(), _("Page doesn't exist.")))->respond();
        }

        $filesystem = $request->getAttribute('clubman.container')['filesystem'];
        $payload->setOutput(new Fractal\Resource\Item($page, new \Domain\Page\PageTransformer($filesystem), 'pages'));

        return (new JSONResponder(new Responder(), $payload))->respond();
    }
}
