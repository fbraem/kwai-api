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
 * Class BrowseUsers
 *
 * Use case to browse all users.
 */
class BrowseUsers
{
    private UserRepository $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    /**
     * Browse all users and returns a list
     *
     * @param BrowseUsersCommand $command
     * @return Entity[]
     * @throws RepositoryException
     */
    public function __invoke(BrowseUsersCommand $command): array
    {
        return $this->userRepo->getAll();
    }
}
