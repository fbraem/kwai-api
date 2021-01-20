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
use Kwai\Modules\News\Domain\Exceptions\StoryNotFoundException;
use Kwai\Modules\News\Domain\Story;
use Kwai\Modules\News\Repositories\StoryRepository;

/**
 * Class GetStory
 *
 * Use case to get a news story with the given id.
 */
class GetStory
{
    /**
     * GetStory constructor.
     *
     * @param StoryRepository $repo
     * @param ImageRepository $imageRepo
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
     * @return GetStory
     */
    public static function create(
        StoryRepository $repo,
        ImageRepository $imageRepo
    ): self {
        return new self($repo, $imageRepo);
    }

    /**
     * Get a story
     *
     * @param GetStoryCommand $command
     * @return Entity<Story>
     * @throws StoryNotFoundException
     * @throws RepositoryException
     */
    public function __invoke(GetStoryCommand $command): Entity
    {
        $story = $this->repo->getById($command->id);
        $images = $this->imageRepo->getImages($command->id);
        /** @noinspection PhpUndefinedMethodInspection */
        $story->attachImages($images);
        return $story;
    }
}
