<?php

namespace REST\News\Actions;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Aura\Payload\Payload;

use Core\Responders\Responder;
use Core\Responders\JSONResponder;

use League\Fractal;

class BrowseStoryAction implements \Core\ActionInterface
{
    public function __invoke(RequestInterface $request, Payload $payload) : ResponseInterface
    {
        $parameters = $request->getAttribute('parameters');

        $db = $request->getAttribute('clubman.container')['db'];
        $dbStories = new \Domain\News\NewsStoriesTable($db);
        $dbStories->orderByDate();

        if (isset($parameters['filter']['category'])) {
            $dbStories->whereCategory($parameters['filter']['category']);
        }

        if (isset($parameters['filter']['year'])) {
            if (isset($parameters['filter']['month'])) {
                $dbStories->wherePublishedYearMonth($parameters['filter']['year'], $parameters['filter']['month']);
            } else {
                $dbStories->wherePublishedYear($parameters['filter']['year']);
            }
        }

        if (isset($parameters['filter']['featured'])) {
            $dbStories->whereFeatured();
        }

        $parameters['filter']['enabled'] = $parameters['filter']['enabled'] ?? 1;
        if ($request->getAttribute('clubman.user') == null || $parameters['filter']['enabled'] == 1) {
            $dbStories->whereAllowedToSee();
        }
        $count = $dbStories->count();

        $limit = $parameters['page']['limit'] ?? 10;
        $offset = $parameters['page']['offset'] ?? 0;

        $stories = $dbStories->find($limit, $offset);

        $payload->setExtras([
            'limit' => $limit,
            'offset' => $offset,
            'count' => $count
        ]);

        $filesystem = $request->getAttribute('clubman.container')['filesystem'];
        $payload->setOutput(new Fractal\Resource\Collection($stories, new \Domain\News\NewsStoryTransformer($filesystem), 'news_stories'));

        return (new JSONResponder(new Responder(), $payload))->respond();
    }
}
