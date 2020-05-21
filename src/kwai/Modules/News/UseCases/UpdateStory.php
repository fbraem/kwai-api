<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Core\Domain\ValueObjects\Locale;
use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Domain\Exceptions\AuthorNotFoundException;
use Kwai\Modules\News\Domain\Exceptions\CategoryNotFoundException;
use Kwai\Modules\News\Domain\Exceptions\StoryNotFoundException;
use Kwai\Modules\News\Domain\Story;
use Kwai\Modules\News\Domain\ValueObjects\Promotion;
use Kwai\Modules\News\Repositories\AuthorRepository;
use Kwai\Modules\News\Repositories\ApplicationRepository;
use Kwai\Modules\News\Repositories\StoryRepository;

/**
 * Class UpdateStory
 *
 * Use case: update a story
 */
class UpdateStory
{
    private StoryRepository $storyRepo;
    private ApplicationRepository $appRepo;
    private AuthorRepository $authorRepo;

    /**
     * CreateStory constructor.
     *
     * @param StoryRepository       $storyRepo
     * @param ApplicationRepository $appRepo
     * @param AuthorRepository      $authorRepo
     */
    public function __construct(
        StoryRepository $storyRepo,
        ApplicationRepository $appRepo,
        AuthorRepository $authorRepo
    ) {
        $this->storyRepo = $storyRepo;
        $this->appRepo = $appRepo;
        $this->authorRepo = $authorRepo;
    }

    /**
     * @param UpdateStoryCommand $command
     * @return Entity
     * @throws AuthorNotFoundException
     * @throws CategoryNotFoundException
     * @throws RepositoryException
     * @throws StoryNotFoundException
     */
    public function __invoke(UpdateStoryCommand $command)
    {
        $story = $this->storyRepo->getById($command->id);
        $app = $this->appRepo->getById($command->application);

        $contents = [];
        foreach ($command->contents as $text) {
            $author = $this->authorRepo->getById($text->author);
            $contents[] = new Text(
                new Locale($text->locale),
                new DocumentFormat($text->format),
                $text->title,
                $text->summary,
                $text->content,
                $author
            );
        }

        $promotion = new Promotion(
            $command->promoted,
            $command->promotion_end_date
                ? Timestamp::createFromString(
                    $command->promotion_end_date,
                    $command->timezone
                ) : null
        );

        /** @noinspection PhpUndefinedMethodInspection */
        $traceableTime = $story->getTraceableTime();
        $traceableTime->markUpdated();

        $story = new Entity(
            $story->id(),
            new Story(
                (object) [
                    'enabled' => $command->enabled,
                    'promotion' => $promotion,
                    'publishTime' => Timestamp::createFromString(
                        $command->publish_date,
                        $command->timezone
                    ),
                    'endDate' => $command->end_date
                        ? Timestamp::createFromString(
                            $command->end_date,
                            $command->timezone
                        ) : null,
                    'remark' => $command->remark,
                    'application' => $app,
                    'contents' => $contents,
                    'traceableTime' => $traceableTime
                ]
            )
        );
        $this->storyRepo->update($story);

        return $story;
    }
}
