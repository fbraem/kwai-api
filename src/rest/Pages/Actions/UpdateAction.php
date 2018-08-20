<?php

namespace REST\Pages\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;

use Domain\Page\PageTransformer;
use Domain\Page\PagesTable;
use REST\Pages\PageValidator;

use Cake\Datasource\Exception\RecordNotFoundException;

class UpdateAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $pagesTable = PagesTable::getTableFromRegistry();
        try {
            $page = $pagesTable->get($args['id'], [
                'contain' => ['Contents', 'Category', 'Contents.User']
            ]);
        } catch (RecordNotFoundException $rnfe) {
            return $response->withStatus(404, _("Page doesn't exist"));
        }

        $data = $request->getParsedBody();
        $validator = new PageValidator();
        if (! $validator->validate($data)) {
            return $response
                ->withStatus(422)
                ->withHeader('content-type', 'application/vnd.api+json')
                ->getBody()
                ->write($validator->toJSON());
        }

        $categoryId = \JmesPath\search('data.relationships.category.data.id', $data);
        if (isset($categoryId)) {
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
        }

        $attributes = \JmesPath\search('data.attributes', $data);

        if (isset($category)) {
            $page->catgory = $category;
        }
        if (isset($attributes['priority'])) {
            $page->priority = $attributes['priority'];
        }
        if (isset($attributes['enabled'])) {
            $page->enabled = $attributes['enabled'];
        }
        if (isset($attributes['remark'])) {
            $page->remark = $attributes['remark'];
        }

        $pagesTable->save($page);

        $filesystem = $this->container->get('filesystem');
        $resource = PageTransformer::createForItem($page, $filesystem);

        $fractal = new Manager();
        $fractal->setSerializer(new JsonApiSerializer(/*$this->baseURL*/));
        $data = $fractal->createData($resource)->toJson();

        return $response
            ->withHeader('content-type', 'application/vnd.api+json')
            ->getBody()
            ->write($data);
    }
}
