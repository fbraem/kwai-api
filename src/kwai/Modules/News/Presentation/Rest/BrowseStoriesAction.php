<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Presentation\Rest;

use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\QueryException;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
use Kwai\Modules\News\Infrastructure\Repositories\StoryImageRepository;
use Kwai\Modules\News\Presentation\Transformers\StoryTransformer;
use Kwai\Modules\News\UseCases\BrowseStories;
use Kwai\Modules\News\UseCases\BrowseStoriesCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class BrowseStoriesAction
 *
 * Action to browse all news stories
 */
class BrowseStoriesAction extends Action
{
    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        $db = $this->getContainerEntry('pdo_db');
        $filesystem = $this->getContainerEntry('filesystem');

        $command = new BrowseStoriesCommand();
        $parameters = $request->getAttribute('parameters');
        $command->limit = $parameters['page']['limit'] ?? 10;
        $command->offset = $parameters['page']['offset'] ?? 0;
        if (isset($parameters['filter']['year'])) {
            $command->publishYear = (int) $parameters['filter']['year'];
        }
        if (isset($parameters['filter']['month'])) {
            $command->publishMonth = (int) $parameters['filter']['month'];
        }
        $command->promoted = isset($parameters['filter']['promoted']);
        if (isset($parameters['filter']['category'])) {
            $command->category = (int) $parameters['filter']['category'];
        }

        try {
            $stories = (new BrowseStories(
                new StoryDatabaseRepository($db),
                new StoryImageRepository($filesystem)
            ))($command);
        } catch (QueryException $e) {
            return (
                new SimpleResponse(500, 'A query exception occurred.')
            )($response);
        }

        $resource = StoryTransformer::createForCollection(
            $stories,
            $this->getContainerEntry('converter')
        );
        $resource->setMeta([
            'limit' => $command->limit,
            'offset' => $command->offset,
            'count' => $stories->getCount()
        ]);

        return (new ResourceResponse(
            $resource
        ))($response);
    }
}
