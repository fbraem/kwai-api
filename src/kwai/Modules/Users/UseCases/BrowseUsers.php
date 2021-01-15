<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Repositories\UserRepository;
use Illuminate\Support\Collection;

/**
 * Class BrowseUsers
 *
 * Use case to browse all users.
 */
class BrowseUsers
{
    /**
     * BrowseUsers constructor.
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
     * @return BrowseUsers
     */
    public static function create(UserRepository $userRepo): BrowseUsers
    {
        return new self($userRepo);
    }

    /**
     * Browse all users and returns a tuple with two values:
     * The real count of users and a collection with users.
     *
     * @param BrowseUsersCommand $command
     * @return array
     * @throws RepositoryException
     */
    public function __invoke(BrowseUsersCommand $command): array
    {
        $all = $this->userRepo->getAll(
            limit: $command->limit,
            offset: $command->offset
        );
        return [
            count($all), new Collection($all)
        ];
    }
}
