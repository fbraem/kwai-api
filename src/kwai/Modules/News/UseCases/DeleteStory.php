<?php
/**
 * @package Kwai
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
    private StoryRepository $repo;

    private ImageRepository $imageRepo;

    /**
     * DeleteStory constructor.
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
