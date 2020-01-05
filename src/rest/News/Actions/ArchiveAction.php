<?php

namespace REST\News\Actions;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

use Core\Responses\JSONResponse;

use Domain\News\NewsStoriesTable;

class ArchiveAction
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $query = NewsStoriesTable::getTableFromRegistry()->find();
        $query->select([
            'year' => $query->func()->year([
                'publish_date' => 'identifier'
            ]),
            'month' => $query->func()->month([
                'publish_date' => 'identifier'
            ]),
            'count' => $query->func()->count('id')
        ]);

        // Don't count news stories which are not yet published
        $query->where(function ($exp, $q) {
            return $exp->lt('publish_date', \Carbon\Carbon::now('UTC')->toDateTimeString());
        });
        // News stories must be enabled
        $query->where(['NewsStories.enabled' => true]);

        $query->group(['year', 'month']);
        $query->order(['year' => 'DESC', 'month' => 'DESC']);

        $archive = $query->all();

        return (new JSONResponse($archive))($response);
    }
}
