<?php
/**
 * @package Modules
 * @subpackage Applications
 */
declare(strict_types=1);

namespace Kwai\Modules\Applications\UseCases;

use Kwai\Core\Domain\Entities;
use Kwai\Core\Infrastructure\Database\QueryException;
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
     * @param BrowseApplicationCommand $command
     * @return Entities
     * @throws QueryException
     */
    public function __invoke(BrowseApplicationCommand $command)
    {
        $query = $this->repo->createQuery();
        if ($command->app) {
            $query->filterApplication($command->app);
        }
        return new Entities($query->count(), $query->execute());
    }
}
