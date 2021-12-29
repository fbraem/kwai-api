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
use Kwai\Modules\Users\Infrastructure\Tables;
use Kwai\Modules\Users\Repositories\UserQuery;
use function Latitude\QueryBuilder\field;

/**
 * Class UserDatabaseQuery
 */
class UserDatabaseQuery extends DatabaseQuery implements UserQuery
{
    public function __construct(Connection $db)
    {
        parent::__construct(
            $db,
            Tables::USERS->column('id')
        );
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query->from((string) Tables::USERS->value);
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        return Tables::USERS->aliases(
            'id',
            'email',
            'password',
            'last_login',
            'first_name',
            'last_name',
            'remark',
            'member_id',
            'uuid',
            'created_at',
            'updated_at',
            'revoked',
            'last_unsuccessful_login'
        );
    }

    /**
     * @inheritDoc
     */
    public function filterById(int $id): UserQuery
    {
        $this->query->andWhere(
            Tables::USERS->field('id')->eq($id)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByUUID(UniqueId $uuid): UserQuery
    {
        $this->query->andWhere(
            Tables::USERS->field('uuid')->eq($uuid)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByEmail(EmailAddress $email): UserQuery
    {
        $this->query->andWhere(
            Tables::USERS->field('email')->eq($email)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $users = new Collection();

        foreach ($rows as $row) {
            $user = Tables::USERS->collect($row);
            $users->put($user->get('id'), $user);
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
            $users[$userId]->put(
                'abilities',
                $abilities->get($userId, new Collection())
            );
        }

        return $users;
    }
}
