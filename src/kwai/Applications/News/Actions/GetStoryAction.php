<?php
/**
 * @package Applications
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Applications\News\Actions;

use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Domain\Exceptions\StoryNotFoundException;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
use Kwai\Modules\News\Infrastructure\Repositories\StoryImageRepository;
use Kwai\Modules\News\Presentation\Transformers\StoryTransformer;
use Kwai\Modules\News\UseCases\GetStory;
use Kwai\Modules\News\UseCases\GetStoryCommand;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class GetStoryAction
 */
class GetStoryAction extends Action
{

    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        $command = new GetStoryCommand();
        $command->id = (int) $args['id'];

        $database = $this->getContainerEntry('pdo_db');
        $filesystem = $this->getContainerEntry('filesystem');
        try {
            $story = GetStory::create(
                new StoryDatabaseRepository($database),
                new StoryImageRepository(
                    $filesystem,
                    $this->getContainerEntry('settings')['files']['url']
                )
            )($command);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (new SimpleResponse(
                500,
                'A repository exception occurred'
            )
            )($response);
        } catch (StoryNotFoundException) {
            return (new NotFoundResponse('Story not found'))($response);
        }

        return (new ResourceResponse(
            StoryTransformer::createForItem(
                $story,
                $this->getContainerEntry('converter')
            )
        ))($response);
    }
}
