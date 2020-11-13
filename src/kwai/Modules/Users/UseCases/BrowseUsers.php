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
use Tightenco\Collect\Support\Collection;

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
     * Browse all users and returns a tuple with two values:
     * The real count of users and a collection with users.
     *
     * @param BrowseUsersCommand $command
     * @return Entity[]
     * @throws RepositoryException
     */
    public function __invoke(BrowseUsersCommand $command): array
    {
        $all = $this->userRepo->getAll();
        return [
            count($all), new Collection($all)
        ];
    }
}
