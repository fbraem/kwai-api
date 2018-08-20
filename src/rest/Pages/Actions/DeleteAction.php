<?php

namespace REST\Pages\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;

use Domain\Page\PageTransformer;
use Domain\Page\PagesTable;
use Domain\Content\ContentsTable;

use Cake\Datasource\Exception\RecordNotFoundException;

class DeleteAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $pageTable = PagesTable::getTableFromRegistry();
        try {
            $page = $pageTable->get($args['id'], [
                'contain' => ['Contents']
            ]);
            $contentTable = ContentsTable::getTableFromRegistry();
            foreach ($page->contents as $content) {
                $contentTable->delete($content);
            }
            $pageTable->delete($page);

            $filesystem = $this->container->get('filesystem');
            $folder = 'images/pages/' . $id;
            $filesystem->deleteDir($folder);

            $response = $response->withStatus(200);
        } catch (RecordNotFoundException $rnfe) {
            $response = $response.withStatus(404, _("Story doesn't exist"));
        }
    }
}
