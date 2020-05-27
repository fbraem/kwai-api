<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Presentation;

use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Database\QueryException;
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
 * Base action class to browse all news stories
 */
abstract class AbstractBrowseStoriesAction extends Action
{
    /**
     * Create the default command for the use case Browse Stories.
     *
     * @param Request $request
     * @param array   $args
     * @return BrowseStoriesCommand
     * @noinspection PhpUnusedParameterInspection
     */
    protected function createCommand(Request $request, array $args): BrowseStoriesCommand
    {
        $command = new BrowseStoriesCommand();
        $parameters = $request->getAttribute('parameters');
        $command->limit = (int) ($parameters['page']['limit'] ?? 10);
        $command->offset = (int) ($parameters['page']['offset'] ?? 0);
        return $command;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        $db = $this->getContainerEntry('pdo_db');
        $filesystem = $this->getContainerEntry('filesystem');

        $command = $this->createCommand($request, $args);

        try {
            [$count, $stories] = (new BrowseStories(
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
            'count' => $count
        ]);

        return (new ResourceResponse(
            $resource
        ))($response);
    }
}
