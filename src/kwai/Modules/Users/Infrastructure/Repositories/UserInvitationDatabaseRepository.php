<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Domain\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Infrastructure\Database;

use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\UserInvitation;
use Kwai\Modules\Users\Infrastructure\Mappers\UserInvitationMapper;
use Kwai\Modules\Users\Infrastructure\Tables;

use Kwai\Modules\Users\Repositories\UserInvitationRepository;

use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\on;
use Latitude\QueryBuilder\Query\SelectQuery;

/**
 * Class UserInvitationDatabaseRepository
 *
 * UserInvitation Repository for read/write UserInvitation entity
 * from/to a database.
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class UserInvitationDatabaseRepository implements UserInvitationRepository
{
    /**
     * The database connection
     */
    private Connection $db;

    /**
     * UserInvitationDatabaseRepository constructor
     *
     * @param Connection $db A database object
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    /**
     * @inheritdoc
     */
    public function getByUniqueId(UniqueId $uuid) : Entity
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $query = $this->createBaseQuery()
            ->where(field(Tables::USER_INVITATIONS()->uuid)->eq(strval($uuid)))
            ->compile()
        ;

        try {
            $row = $this->db->execute($query)->fetch();
        } catch (Database\DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        if ($row) {
            $invitationRow = Tables::USER_INVITATIONS()->createColumnFilter()->filter($row);
            $invitationRow->user = Tables::USERS()->createColumnFilter()->filter($row);
            return UserInvitationMapper::toDomain($invitationRow);
        }
        throw new NotFoundException('UserInvitation');
    }

    /**
     * @inheritdoc
     */
    public function getActive(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function create(UserInvitation $invitation): Entity
    {
        $data = UserInvitationMapper::toPersistence($invitation);

        $query = $this->db->createQueryFactory()
            ->insert((string) Tables::USER_INVITATIONS())
            ->columns(
                ... array_keys($data)
            )
            ->values(
                ... array_values($data)
            )
            ->compile()
        ;
        try {
            $this->db->execute($query);
        } catch (Database\DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        return new Entity(
            $this->db->lastInsertId(),
            $invitation
        );
    }

    /**
     * @inheritdoc
     */
    public function update(Entity $invitation): void
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $invitation->getTraceableTime()->markUpdated();
        $data = UserInvitationMapper::toPersistence($invitation->domain());
        $query = $this->db->createQueryFactory()
            ->update((string) Tables::USER_INVITATIONS(), $data)
            ->where(field('id')->eq($invitation->id()))
            ->compile()
        ;
        try {
            $this->db->execute($query);
        } catch (Database\DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }

    /**
     * Create the base SELECT query
     */
    private function createBaseQuery(): SelectQuery
    {
        $aliasUserFn = Tables::USERS()->getAliasFn();
        $aliasUserInvitationFn = Tables::USER_INVITATIONS()->getAliasFn();

        /** @noinspection PhpUndefinedFieldInspection */
        return $this->db->createQueryFactory()
            ->select(
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
                $aliasUserFn('email'),
                $aliasUserFn('password'),
                $aliasUserFn('last_login'),
                $aliasUserFn('first_name'),
                $aliasUserFn('last_name'),
                $aliasUserFn('remark'),
                $aliasUserFn('uuid'),
                $aliasUserFn('created_at'),
                $aliasUserFn('updated_at')
            )
            ->from((string) Tables::USER_INVITATIONS())
            ->join(
                (string) Tables::USERS(),
                on(
                    Tables::USER_INVITATIONS()->user_id,
                    Tables::USERS()->id
                )
            )
        ;
    }

    /**
     * @inheritDoc
     */
    public function getByEmail(EmailAddress $email): array
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $query = $this->createBaseQuery()
            ->where(field(Tables::USER_INVITATIONS()->email)->eq(strval($email)))
            ->compile()
        ;

        try {
            $rows = $this->db->execute($query)->fetchAll();
        } catch (Database\DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        $userInvitationFilter = Tables::USER_INVITATIONS()->createColumnFilter();
        $userFilter = Tables::USERS()->createColumnFilter();
        return array_map(function ($row) use ($userInvitationFilter, $userFilter) {
            $invitationRow = $userInvitationFilter->filter($row);
            $invitationRow->user = $userFilter->filter($row);
            return UserInvitationMapper::toDomain($invitationRow);
        }, $rows);
    }
}
