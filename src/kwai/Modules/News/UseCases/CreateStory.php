<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\UseCases;

use Illuminate\Support\Collection;
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
use Kwai\Modules\News\Domain\Story;
use Kwai\Modules\News\Domain\ValueObjects\Promotion;
use Kwai\Modules\News\Repositories\ApplicationRepository;
use Kwai\Modules\News\Repositories\StoryRepository;

/**
 * Class CreateStory
 *
 * Use case: create a story
 */
class CreateStory
{
    private StoryRepository $storyRepo;
    private ApplicationRepository $appRepo;
    private ImageRepository $imageRepo;

    /**
     * CreateStory constructor.
     *
     * @param StoryRepository       $storyRepo
     * @param ApplicationRepository $appRepo
     * @param ImageRepository       $imageRepo
     */
    public function __construct(
        StoryRepository $storyRepo,
        ApplicationRepository $appRepo,
        ImageRepository $imageRepo
    ) {
        $this->storyRepo = $storyRepo;
        $this->appRepo = $appRepo;
        $this->imageRepo = $imageRepo;
    }

    /**
     * Factory method
     *
     * @param StoryRepository       $repo
     * @param ApplicationRepository $appRepo
     * @param ImageRepository       $imageRepo
     * @return static
     */
    public static function create(
        StoryRepository $repo,
        ApplicationRepository $appRepo,
        ImageRepository $imageRepo
    ): self {
        return new self($repo, $appRepo, $imageRepo);
    }

    /**
     * @param CreateStoryCommand $command
     * @param Creator            $creator
     * @return Entity<Story>
     * @throws ApplicationNotFoundException
     * @throws RepositoryException
     */
    public function __invoke(CreateStoryCommand $command, Creator $creator): Entity
    {
        $app = $this->appRepo->getById($command->application);

        $contents = new Collection();
        foreach ($command->contents as $text) {
            $contents->push(new Text(
                new Locale($text->locale),
                new DocumentFormat($text->format),
                $text->title,
                $text->summary,
                $text->content,
                $creator
            ));
        }

        $promotion = new Promotion(
            $command->promotion,
            $command->promotion_end_date
                ? new LocalTimestamp(
                    Timestamp::createFromString($command->promotion_end_date),
                    $command->timezone
                ) : null
        );

        $story = $this->storyRepo->create(new Story(
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
            contents: $contents
        ));

        $images = $this->imageRepo->getImages($story->id());
        /** @noinspection PhpUndefinedMethodInspection */
        $story->attachImages($images);

        return $story;
    }
}
