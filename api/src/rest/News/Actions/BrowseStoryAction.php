<?php

namespace REST\News\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;

class BrowseStoryAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $parameters = $request->getAttribute('parameters');

        $query = \Domain\News\NewsStoriesTable::getTableFromRegistry()->find();
        $query->contain(['Contents', 'Category']);
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
        } else {
            $query->contain('Contents.User');
        }

        $count = $query->count();

        $limit = $parameters['page']['limit'] ?? 10;
        $offset = $parameters['page']['offset'] ?? 0;

        $query->limit($limit);
        $query->offset($offset);

        $stories = $query->all();

        $payload->setExtras([
            'limit' => $limit,
            'offset' => $offset,
            'count' => $count
        ]);

        $filesystem = $request->getAttribute('clubman.container')['filesystem'];
        $payload->setOutput(\Domain\News\NewsStoryTransformer::createForCollection($stories, $filesystem));

        return (
            new JSONResponder(
                new Responder(),
                $payload
            )
        )->respond();
    }
}
