<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\UseCases;

use Kwai\Core\Infrastructure\Repositories\ImageRepository;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Domain\Exceptions\StoryNotFoundException;
use Kwai\Modules\News\Repositories\StoryRepository;

/**
 * Class DeleteStory
 *
 * Use case to remove a news story with the given id.
 */
class DeleteStory
{
    /**
     * DeleteStory constructor.
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
     * @return DeleteStory
     */
    public static function create(
        StoryRepository $repo,
        ImageRepository $imageRepo
    ): self {
        return new self($repo, $imageRepo);
    }

    /**
     * Remove a story
     *
     * @param DeleteStoryCommand $command
     * @throws RepositoryException
     * @throws StoryNotFoundException
     */
    public function __invoke(DeleteStoryCommand $command)
    {
        $story = $this->repo->getById($command->id);
        $this->repo->remove($story);
        $this->imageRepo->removeImages($command->id);
    }
}
