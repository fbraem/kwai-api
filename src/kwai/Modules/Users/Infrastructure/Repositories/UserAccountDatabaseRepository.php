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
use Kwai\Modules\Users\Infrastructure\Tables;
use Kwai\Modules\Users\Repositories\UserAccountRepository;
use function Latitude\QueryBuilder\alias;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\func;

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
        $data = UserAccountMapper::toPersistence($account->domain());
        $query = $this->db->createQueryFactory()
            ->update((string) Tables::USERS())
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
    public function create(UserAccount $account): Entity
    {
        $data = UserAccountMapper::toPersistence($account);

        $query = $this->db->createQueryFactory()
            ->insert((string) Tables::USERS())
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
        /** @noinspection PhpUndefinedFieldInspection */
        $query = $this->db->createQueryFactory()
            ->select(
                alias(
                    func('COUNT', Tables::USERS()->id),
                    'c'
                )
            )
            ->from((string) Tables::USERS())
            ->where(field('email')->eq(strval($email)))
        ;
        try {
            $count = collect($this->db->execute($query)->fetch());
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        return $count->get('c') > 0;
    }
}
