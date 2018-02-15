<?php

namespace REST\Pages\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;

use League\Fractal;

class BrowseAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $parameters = $request->getAttribute('parameters');

        $db = $request->getAttribute('clubman.container')['db'];
        $dbPages = new \Domain\Page\PagesTable($db);
        $dbPages->orderByDate();

        if (isset($parameters['filter']['category'])) {
            $dbPages->whereCategory($parameters['filter']['category']);
        }

        $parameters['filter']['enabled'] = $parameters['filter']['enabled'] ?? 1;
        if ($request->getAttribute('clubman.user') == null || $parameters['filter']['enabled'] == 1) {
            $dbPages->whereAllowedToSee();
        }

        if (isset($parameters['filter']['user'])) {
            $dbPages->whereUser($parameters['filter']['user']);
        }

        $count = $dbPages->count();

        $limit = $parameters['page']['limit'] ?? 10;
        $offset = $parameters['page']['offset'] ?? 0;

        $pages = $dbPages->find($limit, $offset);

        $payload->setExtras([
            'limit' => $limit,
            'offset' => $offset,
            'count' => $count
        ]);

        $filesystem = $request->getAttribute('clubman.container')['filesystem'];
        $payload->setOutput(new Fractal\Resource\Collection($pages, new \Domain\Page\PageTransformer($filesystem), 'pages'));

        return (new JSONResponder(new Responder(), $payload))->respond();
    }
}
