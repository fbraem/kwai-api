<?php

namespace REST\News\Actions;

use Interop\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Domain\News\NewsStoryTransformer;
use Domain\News\NewsStoriesTable;

use Core\Responses\ResourceResponse;

class BrowseStoryAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $parameters = $request->getAttribute('parameters');

        $query = NewsStoriesTable::getTableFromRegistry()->find();
        $query->contain(['Contents', 'Category', 'Contents.User']);
        $query->order(['NewsStories.publish_date' => 'DESC']);

        if (isset($parameters['filter']['category'])) {
            $query->where(['Category.id' => $parameters['filter']['category']]);
        }

        // Don't show disabled or unpublished stories
        $parameters['filter']['enabled'] = $parameters['filter']['enabled'] ?? 1;
        if ($request->getAttribute('clubman.user') == null || $parameters['filter']['enabled'] == 1) {
            $query
                ->where(['NewsStories.enabled' => true])
                ->where(
                    function ($exp, $q) {
                        return $exp->or_(function ($or) {
                            return $or
                                ->isNull('NewsStories.publish_date')
                                ->lte('NewsStories.publish_date', \Carbon\Carbon::now('UTC')->toDateTimeString());
                        });
                    }
                );
        }

        if (isset($parameters['filter']['year'])) {
            $query->where([
                'YEAR(NewsStories.publish_date)' => $parameters['filter']['year']
            ]);
            if (isset($parameters['filter']['month'])) {
                $query->where([
                    'MONTH(NewsStories.publish_date)' => $parameters['filter']['month']
                ]);
            }
        }

        if (isset($parameters['filter']['featured'])) {
            $query->where(['NewsStories.featured >' => 0]);
        }

        // Don't show stories which end date is passed
        $query->where(function ($exp, $q) {
            return $exp->or_(function ($or) {
                return $or
                    ->isNull('NewsStories.end_date')
                    ->gt('NewsStories.end_date', \Carbon\Carbon::now('UTC')->toDateTimeString());
            });
        });

        if (isset($parameters['filter']['user'])) {
            $query->matching('Contents.User', function ($q) use ($parameters) {
                return $q->where(['User.id' => $parameters['filter']['user']]);
            });
        }

        $count = $query->count();

        $limit = $parameters['page']['limit'] ?? 10;
        $offset = $parameters['page']['offset'] ?? 0;

        $query->limit($limit);
        $query->offset($offset);

        $stories = $query->all();

        $filesystem = $this->container->get('filesystem');
        $resource = NewsStoryTransformer::createForCollection($stories, $filesystem);
        $resource->setMeta([
            'limit' => intval($limit),
            'offset' => intval($offset),
            'count' => $count
        ]);

        return (new ResourceResponse(
            $resource
        ))($response);
    }
}
