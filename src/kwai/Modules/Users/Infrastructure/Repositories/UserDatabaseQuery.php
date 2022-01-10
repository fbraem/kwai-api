<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Modules\Users\Infrastructure\Mappers\UserDTO;
use Kwai\Modules\Users\Infrastructure\UsersTable;
use Kwai\Modules\Users\Repositories\UserQuery;

/**
 * Class UserDatabaseQuery
 */
class UserDatabaseQuery extends DatabaseQuery implements UserQuery
{
    public function __construct(Connection $db)
    {
        parent::__construct(
            $db,
            UsersTable::column('id')
        );
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query->from(UsersTable::name());
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        return UsersTable::aliases();
    }

    /**
     * @inheritDoc
     */
    public function filterById(int $id): UserQuery
    {
        $this->query->andWhere(
            UsersTable::field('id')->eq($id)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByUUID(UniqueId $uuid): UserQuery
    {
        $this->query->andWhere(
            UsersTable::field('uuid')->eq($uuid)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByEmail(EmailAddress $email): UserQuery
    {
        $this->query->andWhere(
            UsersTable::field('email')->eq($email)
        );
        return $this;
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return Collection<UserDTO>
     * @throws QueryException
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $users = new Collection();

        foreach ($rows as $row) {
            $user = UsersTable::createFromRow($row);
            $users->put(
                $user->id,
                new UserDTO($user)
            );
        }

        if ($rows->isEmpty()) {
            return new Collection();
        }

        $userIds = $users->keys()->toArray();

        // Get the abilities of all users
        $abilityQuery = new UserAbilityDatabaseQuery($this->db);
        $abilityQuery->filterByUser(...$userIds);
        $abilities = $abilityQuery->execute();
        foreach ($userIds as $userId) {
            if ($abilities->has($userId)) {
                $users[$userId]->abilities = $abilities->get($userId);
            }
        }

        return $users;
    }
}
