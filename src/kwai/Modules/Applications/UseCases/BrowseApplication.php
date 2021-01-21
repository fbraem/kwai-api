<?php
/**
 * @package Modules
 * @subpackage Applications
 */
declare(strict_types=1);

namespace Kwai\Modules\Applications\UseCases;

use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Applications\Infrastructure\Repositories\ApplicationDatabaseRepository;
use Kwai\Modules\Applications\Repositories\ApplicationRepository;

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
     * Factory method
     *
     * @param ApplicationDatabaseRepository $repo
     * @return static
     */
    public static function create(ApplicationDatabaseRepository $repo): self
    {
        return new self($repo);
    }

    /**
     * @param BrowseApplicationCommand $command
     * @return array (int, Collection)
     * @throws QueryException
     * @throws RepositoryException
     */
    public function __invoke(BrowseApplicationCommand $command): array
    {
        $query = $this->repo->createQuery();
        if ($command->app) {
            $query->filterApplication($command->app);
        }

        return [
            $query->count(),
            $this->repo->getAll($query)
        ];
    }
}
