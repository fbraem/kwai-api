<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Applications\Author\Actions;

use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Domain\Exceptions\ApplicationNotFoundException;
use Kwai\Modules\News\Domain\Exceptions\AuthorNotFoundException;
use Kwai\Modules\News\Infrastructure\Repositories\AuthorDatabaseRepository;
use Kwai\Modules\News\Infrastructure\Repositories\ApplicationDatabaseRepository;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
use Kwai\Modules\News\Infrastructure\Repositories\StoryImageRepository;
use Kwai\Modules\News\Presentation\Transformers\StoryTransformer;
use Kwai\Modules\News\UseCases\CreateStory;
use Kwai\Modules\News\UseCases\CreateStoryCommand;
use Nette\Schema\ValidationException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * Class CreateStoryAction
 */
class CreateStoryAction extends SaveStoryAction
{
    protected function createCommand(): CreateStoryCommand
    {
        return new CreateStoryCommand();
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

        $user = $request->getAttribute('kwai.user');
        foreach ($command->contents as $content) {
            $content->author = $user->id();
        }

        $database = $this->getContainerEntry('pdo_db');
        $storyRepo = new StoryDatabaseRepository($database);
        $categoryRepo = new ApplicationDatabaseRepository($database);
        $authorRepo = new AuthorDatabaseRepository($database);

        $filesystem = $this->getContainerEntry('filesystem');
        $imageRepo = new StoryImageRepository(
            $filesystem,
            $this->getContainerEntry('settings')['files']['url']
        );

        try {
            $story = (new CreateStory(
                $storyRepo,
                $categoryRepo,
                $authorRepo,
                $imageRepo
            ))($command);
        } catch (RepositoryException $e) {
            return (new SimpleResponse(
                500,
                'A repository exception occurred'
            ))($response);
        } catch (AuthorNotFoundException $e) {
            return (new NotFoundResponse('Author not found'))($response);
        } catch (ApplicationNotFoundException $e) {
            return (new NotFoundResponse('Application not found'))($response);
        }

        return (new ResourceResponse(
            StoryTransformer::createForItem(
                $story,
                $this->getContainerEntry('converter')
            )
        ))($response);
    }
}
