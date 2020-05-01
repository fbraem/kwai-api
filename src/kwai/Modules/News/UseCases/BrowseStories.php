<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\UseCases;

use Kwai\Core\Domain\Entities;
use Kwai\Core\Infrastructure\Repositories\ImageRepository;
use Kwai\Core\Infrastructure\Repositories\QueryException;
use Kwai\Modules\News\Repositories\StoryRepository;

/**
 * Class BrowseStories
 *
 * Use case to browse news stories.
 */
class BrowseStories
{
    private StoryRepository $repo;

    private ImageRepository $imageRepo;

    /**
     * BrowseStory constructor.
     *
     * @param StoryRepository $repo
     * @param ImageRepository $imageRepo
     */
    public function __construct(
        StoryRepository $repo,
        ImageRepository $imageRepo
    ) {
        $this->repo = $repo;
        $this->imageRepo = $imageRepo;
    }

    /**
     * Browse stories
     *
     * @param BrowseStoriesCommand $command
     * @return Entities
     * @throws QueryException
     */
    public function __invoke(BrowseStoriesCommand $command): Entities
    {
        $query = $this->repo->createQuery();

        if (isset($command->publishYear)) {
            $query->filterPublishDate($command->publishYear, $command->publishMonth);
        }
        if ($command->promoted) {
            $query->filterPromoted();
        }
        if ($command->category) {
            $query->filterCategory($command->category);
        }
        if ($command->enabled) {
            $query->filterVisible();
        }

        $count = $query->count();

        $stories = $query->execute($command->limit, $command->offset);
        foreach ($stories as $story) {
            $images = $this->imageRepo->getImages($story->id());
            $story->attachImages($images);
        }
        return new Entities($count, $stories);
    }
}
