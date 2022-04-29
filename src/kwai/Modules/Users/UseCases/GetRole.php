<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\RoleNotFoundException;
use Kwai\Modules\Users\Domain\RoleEntity;
use Kwai\Modules\Users\Repositories\RoleRepository;

/**
 * Class GetRole
 *
 * Use case to get a role with the given id.
 */
class GetRole
{
    /**
     * GetRole constructor.
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
     * Get a role
     *
     * @param GetRoleCommand $command
     * @return RoleEntity
     * @throws RepositoryException
     * @throws RoleNotFoundException
     */
    public function __invoke(GetRoleCommand $command): RoleEntity
    {
        return $this->repo->getByID($command->id);
    }
}
