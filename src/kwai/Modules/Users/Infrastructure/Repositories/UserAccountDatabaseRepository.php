<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserAccountNotFoundException;
use Kwai\Modules\Users\Domain\UserAccount;
use Kwai\Modules\Users\Infrastructure\Mappers\UserAccountDTO;
use Kwai\Modules\Users\Infrastructure\Tables;
use Kwai\Modules\Users\Repositories\UserAccountRepository;
use Kwai\Modules\Users\Repositories\UserQuery;
use function Latitude\QueryBuilder\alias;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\func;

/**
 * Class UserAccountDatabaseRepository
 */
class UserAccountDatabaseRepository extends DatabaseRepository implements UserAccountRepository
{
    public function __construct(Connection $db)
    {
        parent::__construct(
            $db,
        fn($item) => UserAccountDTO::toDomain($item)
        );
    }

    /**
     * @inheritDoc
     */
    public function get(EmailAddress $email): Entity
    {
        $query = $this->createQuery()->filterByEmail($email);

        $accounts = $this->getAll($query);
        if ($accounts->isNotEmpty()) {
            return $accounts->first();
        }

        throw new UserAccountNotFoundException($email);
    }

    /**
     * @inheritDoc
     */
    public function update(Entity $account): void
    {
        $query = $this->db->createQueryFactory()
            ->update(Tables::USERS->value)
            ->set(UserAccountDTO::toPersistence($account->domain())->toArray())
            ->where(field('id')->eq($account->id()))
        ;
        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function create(UserAccount $account): Entity
    {
        $data = UserAccountDTO::toPersistence($account);

        $query = $this->db->createQueryFactory()
            ->insert(Tables::USERS->value)
            ->columns(
                ... $data->keys()
            )
            ->values(
                ... $data->values()
            )
        ;
        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        return new Entity(
            $this->db->lastInsertId(),
            $account
        );
    }

    /**
     * @inheritdoc
     */
    public function existsWithEmail(EmailAddress $email): bool
    {
        $query = $this->db->createQueryFactory()
            ->select(
                alias(
                    func('COUNT', Tables::USERS->column('id')),
                    'c'
                )
            )
            ->from(Tables::USERS->value)
            ->where(Tables::USERS->field('email')->eq(strval($email)))
        ;
        try {
            $count = collect($this->db->execute($query)->fetch());
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        return $count->get('c') > 0;
    }

    public function createQuery(): UserQuery
    {
        return new UserDatabaseQuery($this->db);
    }
}
