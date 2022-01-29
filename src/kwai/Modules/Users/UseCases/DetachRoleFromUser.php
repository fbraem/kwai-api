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
use Kwai\Modules\Users\Repositories\RoleRepository;
use Kwai\Modules\Users\Repositories\UserRepository;

/**
 * Class DetachRoleFromUser
 *
 * Use case to detach an role from a user.
 * - Step 1 - Get the user
 * - Step 2 - Get the roles of the user
 * - Step 4 - Remove the role if present
 */
class DetachRoleFromUser
{
    /**
     * DetachRoleFromUser constructor.
     *
     * @param UserRepository $userRepo
     * @param RoleRepository $roleRepo
     */
    public function __construct(
        private UserRepository $userRepo,
        private RoleRepository $roleRepo
    ) {
        $this->userRepo = $userRepo;
        $this->roleRepo = $roleRepo;
    }

    /**
     * Factory method
     *
     * @param UserRepository $userRepo
     * @param RoleRepository $roleRepo
     * @return DetachRoleFromUser
     */
    public static function create(UserRepository $userRepo, RoleRepository $roleRepo): self
    {
        return new self($userRepo, $roleRepo);
    }

    /**
     * @param DetachRoleFromUserCommand $command
     * @return Entity<User>
     * @throws RepositoryException
     * @throws UserNotFoundException
     * @throws RoleNotFoundException
     */
    public function __invoke(DetachRoleFromUserCommand $command): Entity
    {
        $user = $this->userRepo->getByUniqueId(new UniqueId($command->uuid));
        $role = $this->roleRepo->getById($command->roleId);

        $user->removeRole($role);

        $this->userRepo->update($user);

        return $user;
    }
}
