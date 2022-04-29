<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Exceptions\UnprocessableException;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Users\Domain\RoleEntity;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\UserEntity;
use Kwai\Modules\Users\Repositories\RoleRepository;
use Kwai\Modules\Users\Repositories\UserRepository;

/**
 * Class UpdateUser
 *
 * Use case for updating a user.
 */
class UpdateUser
{
    /**
     * Constructor.
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
     * Factory method.
     *
     * @param UserRepository $userRepo
     * @param RoleRepository $roleRepo
     * @return UpdateUser
     */
    public static function create(
        UserRepository $userRepo,
        RoleRepository $roleRepo
    ) : self
    {
        return new self($userRepo, $roleRepo);
    }

    /**
     * Executes the use case.
     *
     * @throws UserNotFoundException
     * @throws RepositoryException
     * @throws QueryException
     * @throws UnprocessableException
     */
    public function __invoke(UpdateUserCommand $command, UserEntity $activeUser): UserEntity
    {
        $user = $this->userRepo->getByUniqueId(new UniqueId($command->uuid));

        if ($command->email && (string) $user->getEmailAddress() != $command->email) {
            $email = new EmailAddress($command->email);
            $userQuery = $this->userRepo->createQuery();
            $userQuery->filterByEmail($email);
            if ( $userQuery->count() > 0 ) {
                throw new UnprocessableException(
                    $email . ' is already in use.'
                );
            }
        }

        $traceableTime = $user->getTraceableTime();
        $traceableTime->markUpdated();

        $user = new UserEntity(
            $user->id(),
            new User(
                uuid: $user->getUuid(),
                emailAddress: $email ?? $user->getEmailAddress(),
                username: new Name($command->first_name, $command->last_name),
                roles: $user->getRoles(),
                remark: $command->remark,
                member: $user->getMember(),
                traceableTime: $traceableTime
            )
        );

        $this->userRepo->update($user);

        if ($command->roles && count($command->roles) > 0) {
            $query = $this->roleRepo->createQuery();
            $query->filterByIds(...$command->roles);
            $roles = $this->roleRepo->getAll($query);

            $rolesToAdd = $roles->diffKeys($user->getRoles());
            $rolesToAdd->each(fn (RoleEntity $role) => $user->addRole($role));
            $this->userRepo->insertRoles($user, $rolesToAdd);

            $rolesToRemove = $user->getRoles()->diffKeys($roles);
            $rolesToRemove->each(fn (RoleEntity $role) => $user->removeRole($role));
            $this->userRepo->deleteRoles($user, $rolesToRemove);
        }

        return $user;
    }
}
