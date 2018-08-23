<?php

namespace REST\Pages\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\Page\PageTransformer;
use Domain\Page\PagesTable;
use REST\Pages\PageValidator;

use Cake\Datasource\Exception\RecordNotFoundException;

class CreateAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        $validator = new PageValidator();
        if (! $validator->validate($data)) {
            return $validator->unprocessableEntityResponse($response);
        }

        $categoryId = \JmesPath\search('data.relationships.category.data.id', $data);
        if (!isset($categoryId)) {
            return $response
                ->withStatus(422)
                ->withHeader('content-type', 'application/vnd.api+json')
                ->getBody()
                ->write(
                    json_encode([
                        'errors' => [
                            'source' => [
                                'pointer' => '/data/relationships/category'
                            ],
                            'title' => _('Category is required')
                        ]
                    ])
                );
        }

        $pagesTable = PagesTable::getTableFromRegistry();
        try {
            $category = $pagesTable->Category->get($categoryId);
        } catch (RecordNotFoundException $rnfe) {
            return $response
                ->withStatus(422)
                ->withHeader('content-type', 'application/vnd.api+json')
                ->getBody()
                ->write(
                    json_encode([
                        'errors' => [
                            'source' => [
                                'pointer' => '/data/relationships/category'
                            ],
                            'title' => _('Category doesn\'t exist')
                        ]
                    ])
                );
        }

        $attributes = \JmesPath\search('data.attributes', $data);

        $page = $pagesTable->newEntity();
        $page->category = $category;
        $page->remark = $attributes['remark'];
        $page->enabled = $attributes['enabled'];
        $page->priority = $attributes['priority'];
        $pagesTable->save($page);

        $filesystem = $this->container->get('filesystem');
        return (
            new \Core\ResourceResponse(
                PageTransformer::createForItem($page, $filesystem)
            )
        )($response)->withStatus(201);
    }
}
