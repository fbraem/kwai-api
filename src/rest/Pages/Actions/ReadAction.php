<?php

namespace REST\Pages\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Page\PageTransformer;
use Domain\Page\PagesTable;

use Cake\Datasource\Exception\RecordNotFoundException;

class ReadAction extends \Core\Action
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        try {
            $page = PagesTable::getTableFromRegistry()->get($args['id'], [
                'contain' => ['Contents', 'Category', 'Contents.User']
            ]);
        } catch (RecordNotFoundException $rnfe) {
            return $response->withStatus(404, _("Story doesn't exist"));
        }

        $filesystem = $this->container->get('filesystem');
        $resource = PageTransformer::createForItem($page, $filesystem);

        return $this->createJSONResponse($response, $resource);
    }
}
