<?php
/**
 * @package Modules
 * @subpackage Pages
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\UseCases;

use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\ImageRepository;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Pages\Repositories\PageRepository;
use Illuminate\Support\Collection;

/**
 * Class BrowsePages
 *
 * Use case for browsing pages.
 */
class BrowsePages
{
    /**
     * BrowsePages constructor.
     *
     * @param PageRepository   $repo
     * @param ImageRepository  $imageRepo
     */
    private function __construct(
        private PageRepository $repo,
        private ImageRepository $imageRepo
    ) {
    }

    /**
     * @param BrowsePagesCommand $command
     * @return array
     * @throws QueryException
     * @throws RepositoryException
     */
    public function __invoke(BrowsePagesCommand $command): array
    {
        $query = $this->repo->createQuery();

        if ($command->enabled) {
            $query->filterVisible();
        }

        if (isset($command->application)) {
            $query->filterApplication($command->application);
        }

        switch ($command->sort) {
            case BrowsePagesCommand::SORT_PRIORITY:
                $query->orderByPriority();
                break;
            case BrowsePagesCommand::SORT_APPLICATION:
                $query->orderByApplication();
                break;
            case BrowsePagesCommand::SORT_CREATION_DATE:
                $query->orderByCreationDate();
                break;
        }

        if ($command->userId) {
            if (is_int($command->userId)) {
                $query->filterUser((int) $command->userId);
            } else {
                $query->filterUser(new UniqueId($command->userId));
            }
        }

        $count = $query->count();

        $pages = $this->repo->getAll($query, $command->limit, $command->offset);
        foreach ($pages as $page) {
            $images = $this->imageRepo->getImages($page->id());
            $page->attachImages($images);
        }
        return [$count, new Collection($pages)];
    }

    /**
     * Factory method to create this use case.
     *
     * @param PageRepository   $repo
     * @param ImageRepository  $imageRepo
     * @return BrowsePages
     */
    public static function create(
        PageRepository $repo,
        ImageRepository $imageRepo
    ): self {
        return new self(
            $repo,
            $imageRepo
        );
    }
}
