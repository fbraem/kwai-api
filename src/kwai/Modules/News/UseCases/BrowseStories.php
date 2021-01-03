<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\UseCases;

use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\ImageRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Domain\Exceptions\AuthorNotFoundException;
use Kwai\Modules\News\Repositories\AuthorRepository;
use Kwai\Modules\News\Repositories\StoryRepository;

/**
 * Class BrowseStories
 *
 * Use case to browse news stories.
 */
class BrowseStories
{
    private StoryRepository $repo;

    private AuthorRepository $authorRepo;

    private ImageRepository $imageRepo;

    /**
     * BrowseStory constructor.
     *
     * @param StoryRepository  $repo
     * @param AuthorRepository $authorRepo
     * @param ImageRepository  $imageRepo
     */
    public function __construct(
        StoryRepository $repo,
        AuthorRepository $authorRepo,
        ImageRepository $imageRepo
    ) {
        $this->repo = $repo;
        $this->authorRepo = $authorRepo;
        $this->imageRepo = $imageRepo;
    }

    /**
     * Browse stories
     *
     * @param BrowseStoriesCommand $command
     * @return array
     * @throws QueryException
     * @throws RepositoryException
     * @throws AuthorNotFoundException
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
            $user = $this
                ->authorRepo
                ->getByUniqueId(new UniqueId($command->userUid))
            ;
            $query->filterUser($user->id());
        }
        $count = $query->count();

        $stories = $this->repo->getAll($query, $command->limit, $command->offset);
        $stories->each(
            fn ($story) => $story->attachImages($this->imageRepo->getImages($story->id()))
        );

        return [$count, $stories];
    }
}
