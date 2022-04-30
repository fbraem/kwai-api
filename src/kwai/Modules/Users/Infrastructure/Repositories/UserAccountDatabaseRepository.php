<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserAccountNotFoundException;
use Kwai\Modules\Users\Domain\UserAccount;
use Kwai\Modules\Users\Domain\UserAccountEntity;
use Kwai\Modules\Users\Infrastructure\Mappers\UserAccountDTO;
use Kwai\Modules\Users\Infrastructure\Mappers\UserDTO;
use Kwai\Modules\Users\Infrastructure\UsersTable;
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
        // TODO: check if this can be handled in a better way.
        //  UserQuery creates UserDTO objects, replace them with a special
        //  one: UserAccountDTO
        parent::__construct(
            $db,
        fn(UserDTO $item) => (new UserAccountDTO($item->user))->createEntity()
        );
    }

    /**
     * @inheritDoc
     */
    public function get(EmailAddress $email): UserAccountEntity
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
    public function update(UserAccountEntity $account): void
    {
        $data = (new UserAccountDTO())->persistEntity($account)
            ->user
            ->collect()
            ->forget('id')
        ;
        $query = $this->db->createQueryFactory()
            ->update(UsersTable::name())
            ->set($data->toArray())
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
    public function create(UserAccount $account): UserAccountEntity
    {
        $data = (new UserAccountDTO())->persist($account)
            ->user
            ->collect()
            ->forget('id')
        ;

        $query = $this->db->createQueryFactory()
            ->insert(UsersTable::name())
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
        return new UserAccountEntity(
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
                    func('COUNT', UsersTable::column('id')),
                    'c'
                )
            )
            ->from(UsersTable::name())
            ->where(
                UsersTable::field('email')->eq((string) $email)
            )
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
