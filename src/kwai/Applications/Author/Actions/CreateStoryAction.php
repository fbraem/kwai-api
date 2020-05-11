<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Applications\Author\Actions;

use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\NotFoundResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\ResourceResponse;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Domain\Exceptions\AuthorNotFoundException;
use Kwai\Modules\News\Domain\Exceptions\CategoryNotFoundException;
use Kwai\Modules\News\Infrastructure\Repositories\AuthorDatabaseRepository;
use Kwai\Modules\News\Infrastructure\Repositories\CategoryDatabaseRepository;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
use Kwai\Modules\News\Presentation\Transformers\StoryTransformer;
use Kwai\Modules\News\UseCases\Content;
use Kwai\Modules\News\UseCases\CreateStory;
use Kwai\Modules\News\UseCases\CreateStoryCommand;
use Nette\Schema\Elements\Structure;
use Nette\Schema\Expect;
use Nette\Schema\Processor;
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
        $categoryRepo = new CategoryDatabaseRepository($database);
        $authorRepo = new AuthorDatabaseRepository($database);

        try {
            $story = (new CreateStory(
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
            return (new NotFoundResponse('Category not found'))($response);
        }

        return (new ResourceResponse(
            StoryTransformer::createForItem(
                $story,
                $this->getContainerEntry('converter')
            )
        ))($response);
    }
}
