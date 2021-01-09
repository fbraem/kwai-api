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
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\Users\Infrastructure\Tables;
use Kwai\Modules\Users\Repositories\UserInvitationQuery;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\on;

/**
 * Class UserInvitationDatabaseQuery
 */
class UserInvitationDatabaseQuery extends DatabaseQuery implements UserInvitationQuery
{
    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->from((string) Tables::USER_INVITATIONS())
            ->join(
            (string) Tables::USERS(),
            on(
                Tables::USER_INVITATIONS()->user_id,
                Tables::USERS()->id
            )
        );
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        $aliasUserFn = Tables::USERS()->getAliasFn();
        $aliasUserInvitationFn = Tables::USER_INVITATIONS()->getAliasFn();

        return [
            $aliasUserInvitationFn('id'),
            $aliasUserInvitationFn('email'),
            $aliasUserInvitationFn('name'),
            $aliasUserInvitationFn('uuid'),
            $aliasUserInvitationFn('expired_at'),
            $aliasUserInvitationFn('expired_at_timezone'),
            $aliasUserInvitationFn('remark'),
            $aliasUserInvitationFn('user_id'),
            $aliasUserInvitationFn('created_at'),
            $aliasUserInvitationFn('updated_at'),
            $aliasUserInvitationFn('confirmed_at'),
            $aliasUserFn('id'),
            $aliasUserFn('first_name'),
            $aliasUserFn('last_name')
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterByUniqueId(UniqueId $uuid): UserInvitationQuery
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::USER_INVITATIONS()->uuid)->eq($uuid)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByEmail(EmailAddress $emailAddress): UserInvitationQuery
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::USER_INVITATIONS()->email)->eq($emailAddress)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $invitations = new Collection();
        $filters = new Collection([
            Tables::USER_INVITATIONS()->getAliasPrefix(),
            Tables::USERS()->getAliasPrefix()
        ]);

        foreach ($rows as $row) {
            [ $invitation, $creator ] = $row->filterColumns($filters);
            $invitation->put('creator', $creator);
            $invitations->put($invitation->get('id'), $invitation);
        }

        return $invitations;
    }
}
