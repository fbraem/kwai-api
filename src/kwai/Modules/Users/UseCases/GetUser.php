<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Users\Domain\UserEntity;
use Kwai\Modules\Users\Repositories\UserRepository;

/**
 * Class GetUser
 *
 * Use case to get a user with the given unique id.
 */
class GetUser
{
    /**
     * GetUser constructor.
     *
     * @param UserRepository $userRepo
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
     * @param GetUserCommand $command
     * @return UserEntity
     * @throws RepositoryException
     * @throws UserNotFoundException
     */
    public function __invoke(GetUserCommand $command): UserEntity
    {
        return $this->userRepo->getByUniqueId(new UniqueId($command->uuid));
    }
}
