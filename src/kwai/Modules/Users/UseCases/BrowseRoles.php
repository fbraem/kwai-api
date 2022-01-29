<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Repositories\RoleRepository;

/**
 * Class BrowseRoles
 *
 * Use case for browsing roles
 */
class BrowseRoles
{
    /**
     * BrowseRoles constructor.
     *
     * @param RoleRepository $repo
     */
    public function __construct(private RoleRepository $repo)
    {
    }

    /**
     * Factory method
     *
     * @param RoleRepository $repo
     * @return static
     */
    public static function create(RoleRepository $repo): self
    {
        return new self($repo);
    }

    /**
     * Get all roles.
     *
     * @param BrowseRolesCommand $command
     * @return array
     * @throws RepositoryException
     * @throws QueryException
     */
    public function __invoke(BrowseRolesCommand $command): array
    {
        $query = $this->repo->createQuery();
        return [
            $query->count(),
            $this->repo->getAll($query)
        ];
    }
}
