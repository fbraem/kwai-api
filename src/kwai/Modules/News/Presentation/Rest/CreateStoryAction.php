<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Presentation\Rest;

use Kwai\Core\Infrastructure\Presentation\Action;
use Kwai\Core\Infrastructure\Presentation\Responses\SimpleResponse;
use Kwai\Core\Infrastructure\Repositories\QueryException;
use Kwai\Modules\News\Domain\Exceptions\AuthorNotFoundException;
use Kwai\Modules\News\Domain\Exceptions\CategoryNotFoundException;
use Kwai\Modules\News\Infrastructure\Repositories\AuthorDatabaseRepository;
use Kwai\Modules\News\Infrastructure\Repositories\CategoryDatabaseRepository;
use Kwai\Modules\News\Infrastructure\Repositories\StoryDatabaseRepository;
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
class CreateStoryAction extends Action
{
    private function createSchema(): Structure
    {
        return Expect::structure([
            'data' => Expect::structure([
                'type' => Expect::string(),
                'attributes' => Expect::Structure([
                    'enabled' => Expect::bool(false),
                    'publish_date' => Expect::string()->required(),
                    'timezone' => Expect::string()->required(),
                    'end_date' => Expect::string(),
                    'promoted' => Expect::int(0),
                    'promotion_end_date' => Expect::string(),
                    'remark' => Expect::string(),
                    'contents' => Expect::arrayOf(Expect::structure([
                        'title' => Expect::string()->required(),
                        'locale' => Expect::string('nl'),
                        'format' => Expect::string('md'),
                        'summary' => Expect::string()->required(),
                        'content' => Expect::string()
                    ]))
                ]),
                'relationships' => Expect::structure([
                    'category' => Expect::structure([
                        'data' => Expect::structure([
                            'type' => Expect::string(),
                            'id' => Expect::string()
                        ])
                    ])->required()
                ])
            ])
        ]);
    }

    private function processInput($data)
    {
        $processor = new Processor();
        return $processor->process($this->createSchema(), $data);
    }

    private function createCommand($data): CreateStoryCommand
    {
        $normalized = $this->processInput($data);

        $command = new CreateStoryCommand();
        $command->enabled = $normalized->data->attributes->enabled;
        $command->category = $normalized->data->relationships->category->data->id;
        $command->promoted = $normalized->data->attributes->promoted;
        $command->promotion_end_date = $normalized->data->attributes->promotion_end_date ?? null;
        $command->end_date = $normalized->data->attributes->end_date ?? null;
        $command->publish_date = $normalized->data->attributes->publish_date;
        $command->timezone = $normalized->data->attributes->timezone;
        $command->remark = $normalized->data->attributes->remark ?? null;
        foreach ($normalized->data->attributes->contents as $content) {
            $c = new Content();
            $c->content = $content->content;
            $c->format = $content->format;
            $c->locale = $content->locale;
            $c->summary = $content->summary;
            $c->title = $content->title;
            $command->contents[] = $c;
        }

        return $command;
    }

    /**
     * @inheritDoc
     */
    public function __invoke(Request $request, Response $response, array $args)
    {
        try {
            $command = $this->createCommand($request->getParsedBody());
        } catch (ValidationException $ve) {
            return (new SimpleResponse(422, $ve->getMessage()))($response);
        }

        $user = $request->getAttribute('kwai.user');
        foreach ($command->contents as $content) {
            $content->author = $user->id;
        }

        $database = $this->getContainerEntry('pdo_db');
        $storyRepo = new StoryDatabaseRepository($database);
        $categoryRepo = new CategoryDatabaseRepository($database);
        $authorRepo = new AuthorDatabaseRepository($database);

        try {
            (new CreateStory(
                $storyRepo,
                $categoryRepo,
                $authorRepo
            ))($command);
        } catch (QueryException $e) {
        } catch (AuthorNotFoundException $e) {
        } catch (CategoryNotFoundException $e) {
        }
    }
}
