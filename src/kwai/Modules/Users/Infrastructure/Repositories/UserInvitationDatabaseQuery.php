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
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Modules\Users\Infrastructure\Mappers\UserInvitationDTO;
use Kwai\Modules\Users\Infrastructure\UserInvitationsTable;
use Kwai\Modules\Users\Infrastructure\UsersTable;
use Kwai\Modules\Users\Repositories\UserInvitationQuery;
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
        $this->query->from(UserInvitationsTable::name())
            ->join(
                UsersTable::name(),
                on(
                    UserInvitationsTable::column('user_id'),
                    UsersTable::column('id')
                )
            );
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        return [
            ...UserInvitationsTable::aliases(),
            ...UsersTable::aliases()
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterByUniqueId(UniqueId $uuid): UserInvitationQuery
    {
        $this->query->andWhere(
            UserInvitationsTable::field('uuid')->eq($uuid)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterByEmail(EmailAddress $emailAddress): UserInvitationQuery
    {
        $this->query->andWhere(
            UserInvitationsTable::field('email')->eq($emailAddress)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterActive(Timestamp $timestamp): UserInvitationQuery
    {
        $this->query->andWhere(
            UserInvitationsTable::field('expired_at')->gt((string) $timestamp)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterConfirmed(bool $confirmed): UserInvitationQuery
    {
        if ($confirmed) {
            $criteria = UserInvitationsTable::field('confirmed_at')->isNotNull();
        } else {
            $criteria = UserInvitationsTable::field('confirmed_at')->isNull();
        }
        $this->query->andWhere($criteria);
        return $this;
    }

    /**
     * @param int|null $limit
     * @param int|null $offset
     * @return Collection<UserInvitationDTO>
     * @throws QueryException
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $invitations = new Collection();
        foreach ($rows as $row) {
            $invitation = new UserInvitationDTO(
            UserInvitationsTable::createFromRow($row),
            UsersTable::createFromRow($row)
            );
            $invitations->push($invitation);
        }

        return $invitations;
    }
}
