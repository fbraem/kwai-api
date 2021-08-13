<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\UseCases;

use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\ImageRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Repositories\StoryRepository;

/**
 * Class BrowseStories
 *
 * Use case to browse news stories.
 */
class BrowseStories
{
    /**
     * BrowseStory constructor.
     *
     * @param StoryRepository  $repo
     * @param ImageRepository  $imageRepo
     */
    public function __construct(
        private StoryRepository $repo,
        private ImageRepository $imageRepo
    ) {
    }

    /**
     * Factory method
     *
     * @param StoryRepository $repo
     * @param ImageRepository $imageRepo
     * @return static
     */
    public static function create(
        StoryRepository $repo,
        ImageRepository $imageRepo
    ): self {
        return new self($repo, $imageRepo);
    }

    /**
     * Browse stories
     *
     * @param BrowseStoriesCommand $command
     * @return array
     * @throws RepositoryException
     * @throws QueryException
     */
    public function __invoke(BrowseStoriesCommand $command): array
    {
        $query = $this->repo->createQuery();

        if (isset($command->publishYear)) {
            $query->filterPublishDate($command->publishYear, $command->publishMonth);
        }
        if ($command->promoted) {
            $query->filterPromoted();
        }
        if ($command->application) {
            $query->filterApplication($command->application);
        }
        if ($command->enabled) {
            $query->filterVisible();
        }

        if ($command->userUid) {
            if (is_int($command->userUid)) {
                $query->filterUser($command->userUid);
            } else {
                $query->filterUser(new UniqueId($command->userUid));
            }
        }

        $query->orderByPublishDate();

        $count = $query->count();

        $stories = $this->repo->getAll($query, $command->limit, $command->offset);
        $stories->each(
            fn ($story) => $story->attachImages($this->imageRepo->getImages($story->id()))
        );

        return [$count, $stories];
    }
}
