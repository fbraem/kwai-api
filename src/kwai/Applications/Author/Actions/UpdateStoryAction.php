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
use Kwai\Modules\News\Domain\Exceptions\AuthorNotFoundException;
use Kwai\Modules\News\Domain\Exceptions\CategoryNotFoundException;
use Kwai\Modules\News\Domain\Exceptions\StoryNotFoundException;
use Kwai\Modules\News\Infrastructure\Repositories\AuthorDatabaseRepository;
use Kwai\Modules\News\Infrastructure\Repositories\ApplicationDatabaseRepository;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
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
        foreach ($command->contents as $content) {
            $content->author = $user->id();
        }

        $database = $this->getContainerEntry('pdo_db');
        $storyRepo = new StoryDatabaseRepository($database);
        $categoryRepo = new ApplicationDatabaseRepository($database);
        $authorRepo = new AuthorDatabaseRepository($database);

        try {
            $story = (new UpdateStory(
                $storyRepo,
                $categoryRepo,
                $authorRepo
            ))($command);
        } catch (RepositoryException $e) {
            return (new SimpleResponse(
                500,
                'A repository exception occurred'
            ))($response);
        } catch (AuthorNotFoundException $e) {
            return (new NotFoundResponse('Author not found'))($response);
        } catch (CategoryNotFoundException $e) {
            return (new NotFoundResponse('Application not found'))($response);
        } catch (StoryNotFoundException $e) {
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
