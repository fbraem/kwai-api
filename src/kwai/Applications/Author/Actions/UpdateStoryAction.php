<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Applications\Author\Actions;

use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Domain\Exceptions\ApplicationNotFoundException;
use Kwai\Modules\News\Domain\Exceptions\StoryNotFoundException;
use Kwai\Modules\News\Infrastructure\Repositories\ApplicationDatabaseRepository;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
use Kwai\Modules\News\Infrastructure\Repositories\StoryImageRepository;
use Kwai\Modules\News\Presentation\Transformers\StoryTransformer;
use Kwai\Modules\News\UseCases\UpdateStory;
use Kwai\Modules\News\UseCases\UpdateStoryCommand;
use Nette\Schema\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class UpdateStoryAction
 */
class UpdateStoryAction extends SaveStoryAction
{
    /**
     * @return UpdateStoryCommand
     */
    protected function createCommand(): UpdateStoryCommand
    {
        return new UpdateStoryCommand();
    }

    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        try {
            $command = $this->processInput($request->getParsedBody());
        } catch (ValidationException $ve) {
            return (new SimpleResponse(422, $ve->getMessage()))($response);
        }

        $command->id = (int) $args['id'];

        $user = $request->getAttribute('kwai.user');
        $creator = new Creator($user->id(), $user->getUsername());

        $database = $this->getContainerEntry('pdo_db');
        $storyRepo = new StoryDatabaseRepository($database);
        $categoryRepo = new ApplicationDatabaseRepository($database);

        $filesystem = $this->getContainerEntry('filesystem');
        $imageRepo = new StoryImageRepository(
            $filesystem,
            $this->getContainerEntry('settings')['files']['url']
        );

        try {
            $story = (new UpdateStory(
                $storyRepo,
                $categoryRepo,
                $imageRepo
            ))($command, $creator);
        } catch (RepositoryException $e) {
            $this->logException($e);
            return (new SimpleResponse(
                500,
                'A repository exception occurred'
            ))($response);
        } catch (ApplicationNotFoundException) {
            return (new NotFoundResponse('Application not found'))($response);
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
