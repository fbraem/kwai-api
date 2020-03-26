<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Repositories\UserRepository;

/**
 * Class GetUser
 *
 * Use case to get a user with the given unique id.
 */
class GetUser
{
    private UserRepository $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * Get a user
     *
     * @param GetUserCommand $command
     * @return Entity<User>
     * @throws NotFoundException
     * @throws RepositoryException
     */
    public function __invoke(GetUserCommand $command): Entity
    {
        return $this->userRepo->getByUUID(new UniqueId($command->uuid));
    }
}
