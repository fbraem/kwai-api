<?php
/**
 * @package Pages
 * @subpackage UseCases
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\UseCases;

use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\ImageRepository;
use Kwai\Modules\Pages\Repositories\PageRepository;
use Tightenco\Collect\Support\Collection;

/**
 * Class BrowsePages
 *
 * Use case for browsing pages.
 */
class BrowsePages
{
    private PageRepository $repo;

    private ImageRepository $imageRepo;

    /**
     * BrowsePages constructor.
     *
     * @param PageRepository  $repo
     * @param ImageRepository $imageRepo
     */
    private function __construct(PageRepository $repo, ImageRepository $imageRepo)
    {
        $this->repo = $repo;
        $this->imageRepo = $imageRepo;
    }

    /**
     * @param BrowsePagesCommand $command
     * @return array
     * @throws QueryException
     */
    public function __invoke(BrowsePagesCommand $command): array
    {
        $query = $this->repo->createQuery();

        if (isset($command->enabled)) {
            $query->filterVisible();
        }

        if (isset($command->application)) {
            $query->filterApplication($command->application);
        }

        $count = $query->count();

        $pages = $query->execute($command->limit, $command->offset);
        foreach ($pages as $page) {
            $images = $this->imageRepo->getImages($page->id());
            $page->attachImages($images);
        }
        return [$count, new Collection($pages)];
    }

    /**
     * Factory method to create this use case.
     *
     * @param PageRepository  $repo
     * @param ImageRepository $imageRepo
     * @return BrowsePages
     */
    public static function create(PageRepository $repo, ImageRepository $imageRepo)
    {
        return new self(
            $repo,
            $imageRepo
        );
    }
}
