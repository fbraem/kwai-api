<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Users\Repositories\UserRepository;

/**
 * Class GetUserRoles
 *
 * Use case that returns all roles of a user with the given uuid
 */
class GetUserRoles
{
    /**
     * GetUserRoles constructor.
     *
     * @param UserRepository    $userRepo
     */
    public function __construct(private UserRepository $userRepo)
    {
    }

    /**
     * Factory method
     *
     * @param UserRepository $userRepo
     * @return static
     */
    public static function create(UserRepository $userRepo): self
    {
        return new self($userRepo);
    }

    /**
     * Get a user
     *
     * @param GetUserRolesCommand $command
     * @return Collection
     * @throws RepositoryException
     * @throws UserNotFoundException
     */
    public function __invoke(GetUserRolesCommand $command): Collection
    {
        $user = $this->userRepo->getByUniqueId(new UniqueId($command->uuid));
        return $user->getRoles();
    }
}
