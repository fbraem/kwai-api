<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Repositories\UserRepository;

/**
 * Class GetUser
 *
 * Use case to get a specific user.
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
     * @throws RepositoryException
     * @return Entity[]
     */
    public function __invoke(GetUserCommand $command): array
    {
        return $this->userRepo->getAll();
    }
}
