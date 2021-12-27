<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Repositories\UserAccountRepository;

/**
 * Class BrowseUserAccounts
 *
 * Use case for browsing all user accounts.
 */
class BrowseUserAccounts
{
    /**
     * Constructor.
     * @param UserAccountRepository $repo
     */
    public function __construct(
        private UserAccountRepository $repo
    ) {
    }

    /**
     * Factory method to create a new BrowseUserAccounts usecase.
     * @param UserAccountRepository $repo
     * @return BrowseUserAccounts
     */
    public static function create(UserAccountRepository $repo)
    {
        return new self($repo);
    }

    /**
     * @throws RepositoryException
     * @throws QueryException
     */
    public function __invoke(BrowseUserAccountsCommand $command): array
    {
        $query = $this->repo->createQuery();

        return [
            $query->count(),
            $this->repo->getAll($query, $command->limit, $command->offset)
        ];
    }
}
