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
use Kwai\Modules\Users\Infrastructure\UsersTableSchema;
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
            UsersTableSchema::column('id')
        );
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query->from(UsersTableSchema::getTableName());
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        return UsersTableSchema::aliases();
    }

    /**
     * @inheritDoc
     */
    public function filterById(int $id): UserQuery
    {
        $this->query->andWhere(
            UsersTableSchema::field('id')->eq($id)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByUUID(UniqueId $uuid): UserQuery
    {
        $this->query->andWhere(
            UsersTableSchema::field('uuid')->eq($uuid)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByEmail(EmailAddress $email): UserQuery
    {
        $this->query->andWhere(
            UsersTableSchema::field('email')->eq($email)
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

        /** @var Collection<UserDTO> $users */
        $users = new Collection();

        foreach ($rows as $row) {
            $user = UsersTableSchema::createFromRow($row);
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
            $users[$userId]->abilities->put(
                'abilities',
                $abilities->get($userId, new Collection())
            );
        }

        return $users;
    }
}
