<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\UseCases;

use Kwai\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Core\Domain\ValueObjects\Locale;
use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Infrastructure\Repositories\QueryException;
use Kwai\Modules\News\Domain\Exceptions\AuthorNotFoundException;
use Kwai\Modules\News\Domain\Exceptions\CategoryNotFoundException;
use Kwai\Modules\News\Domain\Story;
use Kwai\Modules\News\Domain\ValueObjects\Promotion;
use Kwai\Modules\News\Repositories\AuthorRepository;
use Kwai\Modules\News\Repositories\CategoryRepository;
use Kwai\Modules\News\Repositories\StoryRepository;

/**
 * Class CreateStory
 *
 * Use case: create a story
 */
class CreateStory
{
    private StoryRepository $storyRepo;
    private CategoryRepository $categoryRepo;
    private AuthorRepository $authorRepo;

    /**
     * CreateStory constructor.
     *
     * @param StoryRepository    $storyRepo
     * @param CategoryRepository $categoryRepo
     * @param AuthorRepository   $authorRepo
     */
    public function __construct(
        StoryRepository $storyRepo,
        CategoryRepository $categoryRepo,
        AuthorRepository $authorRepo
    ) {
        $this->storyRepo = $storyRepo;
        $this->categoryRepo = $categoryRepo;
        $this->authorRepo = $authorRepo;
    }

    /**
     * @param CreateStoryCommand $command
     * @throws QueryException
     * @throws AuthorNotFoundException
     * @throws CategoryNotFoundException
     */
    public function __invoke(CreateStoryCommand $command)
    {
        $category = $this->categoryRepo->getById((int) $command->category);

        $contents = [];
        foreach ($command->contents as $text) {
            $author = $this->authorRepo->getById((int) $text->author);
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

        return $this->storyRepo->create(new Story(
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
                'category' => $category,
                'contents' => $contents
            ]
        ));
    }
}
