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

        $query = \Domain\Page\PagesTable::getTableFromRegistry()->find();
        $query->contain(['Contents', 'Contents.User', 'Category']);

        if (isset($parameters['filter']['category'])) {
            $query->where(['Category.id' => $parameters['filter']['category']]);
        }

        $parameters['filter']['enabled'] = $parameters['filter']['enabled'] ?? 1;
        if ($request->getAttribute('clubman.user') == null || $parameters['filter']['enabled'] == 1) {
            $query->where(['enabled' => true]);
        }

        if (isset($parameters['filter']['user'])) {
            $query->matching('Contents.User', function ($q) use ($parameters) {
                return $q->where(['User.id' => $parameters['filter']['user']]);
            });
        } else {
            $query->contain('Contents.User');
        }

        $query->order(['Pages.priority' => 'DESC']);
        $query->order(['Pages.created_at' => 'ASC']);
        $count = $query->count();

        $limit = $parameters['page']['limit'] ?? 10;
        $offset = $parameters['page']['offset'] ?? 0;

        $query->limit($limit);
        $query->offset($offset);

        if (isset($parameters['filter']['user'])) {
            $query->matching('Contents.User', function ($q) use ($parameters) {
                return $q->where(['User.id' => $parameters['filter']['user']]);
            });
        } else {
            $query->contain('Contents.User');
        }

        $pages = $query->all();

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
