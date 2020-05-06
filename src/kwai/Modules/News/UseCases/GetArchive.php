<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\UseCases;

use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Repositories\StoryRepository;

/**
 * Class GetArchive
 *
 * Use case to get archive information: number of stories
 * for each year/month.
 */
class GetArchive
{
    private StoryRepository $repo;

    /**
     * GetArchive constructor.
     *
     * @param StoryRepository $repo
     */
    public function __construct(StoryRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param GetArchiveCommand $command
     * @return array
     * @throws RepositoryException
     */
    public function __invoke(GetArchiveCommand $command)
    {
        return $this->repo->getArchive();
    }
}
