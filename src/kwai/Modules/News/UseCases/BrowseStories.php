<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\ImageRepository;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Domain\Story;
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
     * @return Entity<Story>[]
     * @throws RepositoryException
     */
    public function __invoke(BrowseStoriesCommand $command): array
    {
        $stories = $this->repo->getAll();
        foreach ($stories as $story) {
            $images = $this->imageRepo->getImages($story->id());
            /** @noinspection PhpUndefinedMethodInspection */
            $story->attachImages($images);
        }
        return $stories;
    }
}
