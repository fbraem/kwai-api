<?php
/**
 * @package Modules
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Modules\Club\UseCases;

use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Club\Repositories\TeamRepository;

/**
 * Class BrowseTeams
 */
class BrowseTeams
{
    public function __construct(
        private TeamRepository $repo
    ) {
    }

    public static function create(TeamRepository $repo): self
    {
        return new self($repo);
    }

    /**
     * @throws RepositoryException
     * @throws QueryException
     */
    public function __invoke(BrowseTeamsCommand $command)
    {
        $query = $this->repo->createQuery();

        return [
            $query->count(),
            $this->repo->getAll($query, $command->limit, $command->offset)
        ];
    }
}
