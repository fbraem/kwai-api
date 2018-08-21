<?php

namespace REST\Pages\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Page\PageTransformer;
use Domain\Page\PagesTable;

class BrowseAction extends \Core\Action
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $parameters = $request->getAttribute('parameters');

        $query = PagesTable::getTableFromRegistry()->find();
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
        }

        $query->order(['Pages.priority' => 'DESC']);
        $query->order(['Pages.created_at' => 'ASC']);

        $count = $query->count();

        $limit = $parameters['page']['limit'] ?? 10;
        $offset = $parameters['page']['offset'] ?? 0;

        $query->limit($limit);
        $query->offset($offset);

        $pages = $query->all();

        $filesystem = $this->container->get('filesystem');
        $resource = PageTransformer::createForCollection($pages, $filesystem);
        $resource->setMeta([
            'limit' => $limit,
            'offset' => $offset,
            'count' => $count
        ]);

        return $this->createJSONResponse($response, $resource);
    }
}
