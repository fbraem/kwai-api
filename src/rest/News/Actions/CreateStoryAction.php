<?php

namespace REST\News\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\News\NewsStoryTransformer;
use Domain\News\NewsStoriesTable;

use REST\News\NewsStoryValidator;

class CreateStoryAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();

        $validator = new NewsStoryValidator();
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

        $storiesTable = NewsStoriesTable::getTableFromRegistry();
        try {
            $category = $storiesTable->Category->get($categoryId);
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

        $story = $storiesTable->newEntity();
        $story->category = $category;
        $story->publish_date = $attributes['publish_date'];
        $story->publish_date_timezone = $attributes['publish_date_timezone'] ?? null;
        $story->end_date = $attributes['end_date'] ?? null;
        $story->end_date_timezone = $attributes['end_date_timezone'] ?? null;
        $story->featured = $attributes['featured'] ?? 0;
        $story->featured_end_date = $attributes['featured_end_date'] ?? null;
        $story->featered_end_date_timezone = $attributes['featured_end_date_timezone'] ?? null;
        $story->enabled = $attributes['enabled'];
        $story->remark = $attributes['remark'] ?? null;

        $storiesTable->save($story);
        $filesystem = $this->container->get('filesystem');

        return (new \Core\ResourceResponse(
            NewsStoryTransformer::createForItem($story, $filesystem)
        ))($response)->withStatus(201);
    }
}
