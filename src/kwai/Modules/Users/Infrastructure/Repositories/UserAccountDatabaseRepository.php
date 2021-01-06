<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserAccountNotFoundException;
use Kwai\Modules\Users\Domain\UserAccount;
use Kwai\Modules\Users\Infrastructure\Mappers\UserAccountMapper;
use Kwai\Modules\Users\Repositories\UserAccountRepository;

/**
 * Class UserAccountDatabaseRepository
 */
class UserAccountDatabaseRepository extends DatabaseRepository implements UserAccountRepository
{
    /**
     * @inheritDoc
     */
    public function get(EmailAddress $email): Entity
    {
        $query = new UserDatabaseQuery($this->db);
        $query->filterByEmail($email);

        try {
            $accounts = $query->execute();
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        if ($accounts->isNotEmpty()) {
            $account = $accounts->first();
            return new Entity(
                (int) $account->get('id'),
                UserAccountMapper::toDomain($account)
            );
        }

        throw new UserAccountNotFoundException($email);
    }

    /**
     * @inheritDoc
     */
    public function update(Entity $account): void
    {
        // TODO: Implement update() method.
    }

    /**
     * @inheritDoc
     */
    public function create(UserAccount $account): Entity
    {
        // TODO: Implement create() method.
    }
}
