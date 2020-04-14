<?php

namespace REST\Pages\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Page\PageTransformer;
use Domain\Page\PagesTable;
use Domain\Content\ContentsTable;

use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\OkResponse;

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

            $response = (new OkResponse())($response);
        } catch (RecordNotFoundException $rnfe) {
            $response = (new NotFoundResponse(_("Page doesn't exist")))($response);
        }

        return $response;
    }
}
