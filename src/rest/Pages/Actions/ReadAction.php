<?php

namespace REST\Pages\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Page\PageTransformer;
use Domain\Page\PagesTable;

use Cake\Datasource\Exception\RecordNotFoundException;

use Core\Responses\ResourceResponse;
use Core\Responses\NotFoundResponse;

class ReadAction
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
            $filesystem = $this->container->get('filesystem');

            $response = (new ResourceResponse(
                PageTransformer::createForItem($page, $filesystem)
            ))($response);
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("Page doesn't exist")))($response);
        }
        
        return $response;
    }
}
