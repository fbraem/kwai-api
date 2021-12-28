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
        /** @noinspection PhpUndefinedFieldInspection */
        parent::__construct($db, Tables::USERS()->id);
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query->from((string) Tables::USERS());
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        $aliasFn = Tables::USERS()->getAliasFn();
        return [
            $aliasFn('id'),
            $aliasFn('email'),
            $aliasFn('password'),
            $aliasFn('last_login'),
            $aliasFn('first_name'),
            $aliasFn('last_name'),
            $aliasFn('remark'),
            $aliasFn('member_id'),
            $aliasFn('uuid'),
            $aliasFn('created_at'),
            $aliasFn('updated_at'),
            $aliasFn('revoked'),
            $aliasFn('last_unsuccessful_login')
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterById(int $id): UserQuery
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::USERS()->id)->eq($id)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByUUID(UniqueId $uuid): UserQuery
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::USERS()->uuid)->eq($uuid)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByEmail(EmailAddress $email): UserQuery
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::USERS()->email)->eq($email)
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

        $filters = new Collection([
            Tables::USERS()->getAliasPrefix()
        ]);

        foreach ($rows as $row) {
            [
                $user
            ] = $row->filterColumns($filters);
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
