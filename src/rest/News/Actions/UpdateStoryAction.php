<?php

namespace REST\News\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use League\Fractal\Manager;
use League\Fractal\Serializer\JsonApiSerializer;

use Domain\News\NewsStoryTransformer;
use Domain\News\NewsStoriesTable;
use REST\News\NewsStoryValidator;
use Domain\Category\CategoriesTable;

use Cake\Datasource\Exception\RecordNotFoundException;

class UpdateStoryAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $storiesTable = NewsStoriesTable::getTableFromRegistry();
        try {
            $story = $storiesTable->get($args['id'], [
                'contain' => ['Category']
            ]);
        } catch (RecordNotFoundException $rnfe) {
            $response->withStatus(404, _("Story doesn't exist."));
        }

        $data = $request->getParsedBody();

        $validator = new NewsStoryValidator();
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
                $category = CategoriesTable::getTableFromRegistry()->get($categoryId);
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
            $story->category = $category;
        }
        if (isset($attributes['publish_date'])) {
            $story->publish_date = $attributes['publish_date'];
        }
        if (isset($attributes['end_date'])) {
            $story->end_date = $attributes['end_date'];
        }
        if (isset($attributes['featured'])) {
            $story->featured = $attributes['featured'];
        }
        if (isset($attributes['featured_end_date'])) {
            $story->featured_end_date = $attributes['featured_end_date'];
        }
        if (isset($attributes['enabled'])) {
            $story->enabled = $attributes['enabled'];
        }
        if (isset($attributes['publish_date_timezone'])) {
            $story->publish_date_timezone = $attributes['publish_date_timezone'];
        }
        if (isset($attributes['end_date_timezone'])) {
            $story->end_date_timezone = $attributes['end_date_timezone'];
        }
        if (isset($attributes['featured_date_timezone'])) {
            $story->featured_date_timezone = $attributes['featured_date_timezone'];
        }
        if (isset($attributes['remark'])) {
            $story->remark = $attributes['remark'];
        }

        $storiesTable->save($story);

        $filesystem = $this->container->get('filesystem');
        $resource = NewsStoryTransformer::createForItem($story, $filesystem);

        $fractal = new Manager();
        $fractal->setSerializer(new JsonApiSerializer(/*$this->baseURL*/));
        $data = $fractal->createData($resource)->toJson();

        return $response
            ->withHeader('content-type', 'application/vnd.api+json')
            ->getBody()
            ->write($data);
    }
}
