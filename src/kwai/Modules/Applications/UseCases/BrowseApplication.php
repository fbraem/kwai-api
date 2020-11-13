<?php
/**
 * @package Modules
 * @subpackage Applications
 */
declare(strict_types=1);

namespace Kwai\Modules\Applications\UseCases;

use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Modules\Applications\Repositories\ApplicationRepository;
use Tightenco\Collect\Support\Collection;

/**
 * Class BrowseApplication
 *
 * Use case: browse applications
 */
class BrowseApplication
{
    private ApplicationRepository $repo;

    /**
     * BrowseApplication constructor.
     *
     * @param ApplicationRepository $repo
     */
    public function __construct(ApplicationRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param BrowseApplicationCommand $command
     * @return array (int, Collection)
     * @throws QueryException
     */
    public function __invoke(BrowseApplicationCommand $command): array
    {
        $query = $this->repo->createQuery();
        if ($command->app) {
            $query->filterApplication($command->app);
        }
        return [
            $query->count(),
            new Collection($query->execute())
        ];
    }
}
