<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\RoleNotFoundException;
use Kwai\Modules\Users\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Users\Infrastructure\Repositories\RoleDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Repositories\RoleRepository;
use Kwai\Modules\Users\Repositories\UserRepository;

/**
 * Class AttachRoleToUser
 *
 * Use case to attach a role to a user.
 */
class AttachRoleToUser
{
    /**
     * AttachRoleToUser constructor.
     *
     * @param UserRepository $userRepo
     * @param RoleRepository $roleRepo
     */
    public function __construct(
        private UserRepository $userRepo,
        private RoleRepository $roleRepo
    ) {
    }

    /**
     * Factory method
     *
     * @param UserDatabaseRepository $userRepo
     * @param RoleDatabaseRepository $roleRepo
     * @return static
     */
    public static function create(
        UserDatabaseRepository $userRepo,
        RoleDatabaseRepository $roleRepo
    ): self {
        return new self($userRepo, $roleRepo);
    }

    /**
     * @param AttachRoleToUserCommand $command
     * @return Entity<User>
     * @throws RepositoryException
     * @throws UserNotFoundException
     * @throws RoleNotFoundException
     */
    public function __invoke(AttachRoleToUserCommand $command): Entity
    {
        $user = $this->userRepo->getByUniqueId(new UniqueId($command->uuid));
        $role = $this->roleRepo->getById($command->roleId);
        $user->addRole($role);
        $this->userRepo->update($user);

        return $user;
    }
}
