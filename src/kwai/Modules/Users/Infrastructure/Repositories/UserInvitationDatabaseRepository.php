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
use Kwai\Modules\Users\Infrastructure\UserInvitationsTable;
use Kwai\Modules\Users\Infrastructure\UsersTable;

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
     * The user invitation table
     */
    private UserInvitationsTable $table;

    /**
     * The user table
     */
    private UsersTable $userTable;

    /**
     * UserInvitationDatabaseRepository constructor
     *
     * @param Connection $db A database object
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
        $this->table = new UserInvitationsTable();
        $this->userTable = new UsersTable();
    }

    /**
     * @inheritdoc
     */
    public function getByUniqueId(UniqueId $uuid) : Entity
    {
        $query = $this->createBaseQuery()
            ->where(field($this->table->column('uuid'))->eq(strval($uuid)))
            ->compile()
        ;

        try {
            $row = $this->db->execute($query)->fetch();
        } catch (Database\DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        if ($row) {
            $invitationRow = $this->table->filter($row);
            $invitationRow->user = $this->userTable->filter($row);
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
            ->insert($this->table->from())
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
            ->update($this->table->from(), $data)
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
        $columns = array_merge(
            $this->table->alias(),
            $this->userTable->alias()
        );

        return $this->db->createQueryFactory()
            ->select(... $columns)
            ->from($this->table->from())
            ->join(
                $this->userTable->from(),
                on(
                    $this->table->from() . '.user_id',
                    $this->userTable->from() . '.id'
                )
            )
        ;
    }

    /**
     * @inheritDoc
     */
    public function getByEmail(EmailAddress $email): array
    {
        $query = $this->createBaseQuery()
            ->where(field($this->table->from() . '.email')->eq(strval($email)))
            ->compile()
        ;

        try {
            $rows = $this->db->execute($query)->fetchAll();
        } catch (Database\DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        return array_map(function ($row) {
            $invitationRow = $this->table->filter($row);
            $invitationRow->user = $this->userTable->filter($row);
            return UserInvitationMapper::toDomain($invitationRow);
        }, $rows);
    }
}
