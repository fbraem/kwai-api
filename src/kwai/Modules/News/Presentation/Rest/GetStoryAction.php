<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Presentation\Rest;

use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Domain\Exceptions\StoryNotFoundException;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
use Kwai\Modules\News\Infrastructure\Repositories\StoryImageRepository;
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
        $command->id = $args['id'];

        $database = $this->getContainerEntry('pdo_db');
        $filesystem = $this->getContainerEntry('filesystem');
        try {
            $story = (new GetStory(
                new StoryDatabaseRepository($database),
                new StoryImageRepository($filesystem)
            ))($command);
        } catch (QueryException $e) {
            return (new SimpleResponse(
                500,
                'A repository exception occurred'
            )
            )($response);
        } catch (StoryNotFoundException $e) {
            return (new NotFoundException('Story not found'))($response);
        }

        return (new ResourceResponse(
        ));
    }
}
