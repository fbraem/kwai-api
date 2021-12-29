<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\Timestamp;
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
        $this->query->from(Tables::USER_INVITATIONS->value)
            ->join(
                Tables::USERS->value,
                on(
                    Tables::USER_INVITATIONS->column('user_id'),
                    Tables::USERS->column('id')
                )
            );
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        return [
            ...Tables::USER_INVITATIONS->aliases(
                'id',
                'email',
                'name',
                'uuid',
                'expired_at',
                'expired_at_timezone',
                'remark',
                'user_id',
                'created_at',
                'updated_at',
                'confirmed_at'
            ),
            ...Tables::USERS->aliases(
                'id',
                'first_name',
                'last_name'
            )
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterByUniqueId(UniqueId $uuid): UserInvitationQuery
    {
        $this->query->andWhere(
            Tables::USER_INVITATIONS->field('uuid')->eq($uuid)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByEmail(EmailAddress $emailAddress): UserInvitationQuery
    {
        $this->query->andWhere(
            Tables::USER_INVITATIONS->field('email')->eq($emailAddress)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterActive(Timestamp $timestamp): UserInvitationQuery
    {
        $this->query->andWhere(
            Tables::USER_INVITATIONS->field('expired_at')->gt((string) $timestamp)
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
        foreach ($rows as $row) {
            $invitation = Tables::USER_INVITATIONS->collect($row);
            $invitation->put(
                'creator',
                Tables::USERS->collect($row)
            );
            $invitations->put(
                $invitation->get('id'),
                $invitation
            );
        }

        return $invitations;
    }
}
