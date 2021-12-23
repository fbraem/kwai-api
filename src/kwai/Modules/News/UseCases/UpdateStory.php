<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Core\Domain\ValueObjects\Locale;
use Kwai\Core\Domain\ValueObjects\LocalTimestamp;
use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Infrastructure\Repositories\ImageRepository;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Domain\Exceptions\ApplicationNotFoundException;
use Kwai\Modules\News\Domain\Exceptions\StoryNotFoundException;
use Kwai\Modules\News\Domain\Story;
use Kwai\Modules\News\Domain\ValueObjects\Promotion;
use Kwai\Modules\News\Repositories\ApplicationRepository;
use Kwai\Modules\News\Repositories\StoryRepository;

/**
 * Class UpdateStory
 *
 * Use case: update a story
 */
class UpdateStory
{
    /**
     * CreateStory constructor.
     *
     * @param StoryRepository       $storyRepo
     * @param ApplicationRepository $appRepo
     * @param ImageRepository       $imageRepo
     */
    public function __construct(
        private StoryRepository $storyRepo,
        private ApplicationRepository $appRepo,
        private ImageRepository $imageRepo
    ) {
    }

    /**
     * @param UpdateStoryCommand $command
     * @param Creator            $creator
     * @return Entity
     * @throws ApplicationNotFoundException
     * @throws RepositoryException
     * @throws StoryNotFoundException
     */
    public function __invoke(UpdateStoryCommand $command, Creator $creator)
    {
        $story = $this->storyRepo->getById($command->id);
        $app = $this->appRepo->getById($command->application);

        if ($command->contents) {
            $contents = collect([]);
            foreach ($command->contents as $text) {
                $contents->push(new Text(
                    locale: new Locale($text->locale),
                    format: new DocumentFormat($text->format),
                    title: $text->title,
                    summary: $text->summary,
                    content: $text->content,
                    author: $creator
                ));
            }
        } else {
            $contents = $story->getContents();
        }

        $promotion = new Promotion(
            $command->promotion,
            $command->promotion_end_date
                ? new LocalTimestamp(
                    Timestamp::createFromString($command->promotion_end_date),
                    $command->timezone
                ) : null
        );

        /** @noinspection PhpUndefinedMethodInspection */
        $traceableTime = $story->getTraceableTime();
        $traceableTime->markUpdated();

        $story = new Entity(
            $story->id(),
            new Story(
                enabled: $command->enabled,
                promotion: $promotion,
                publishTime: new LocalTimestamp(
                    Timestamp::createFromString($command->publish_date),
                    $command->timezone
                ),
                endDate: $command->end_date
                    ? new LocalTimestamp(
                        Timestamp::createFromString($command->end_date),
                    $command->timezone
                    ) : null,
                remark: $command->remark,
                application: $app,
                contents: $contents,
                traceableTime: $traceableTime
            )
        );
        $this->storyRepo->update($story);

        $images = $this->imageRepo->getImages($story->id());
        /** @noinspection PhpUndefinedMethodInspection */
        $story->attachImages($images);

        return $story;
    }
}
