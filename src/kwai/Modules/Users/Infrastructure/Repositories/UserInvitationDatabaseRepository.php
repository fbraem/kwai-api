<?php
/**
 * @package kwai
 * @subpackage Auth
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Domain\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Infrastructure\Database;

use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Modules\Users\Domain\UserInvitation;
use Kwai\Modules\Users\Infrastructure\Mappers\UserInvitationMapper;
use Kwai\Modules\Users\Infrastructure\UserInvitationsTable;
use Kwai\Modules\Users\Infrastructure\UsersTable;

use Kwai\Modules\Users\Repositories\UserInvitationRepository;

use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\on;
use Latitude\QueryBuilder\Query\SelectQuery;

/**
 * UserInvitation Repository for read/write UserInvitation entity
 * from/to a database.
 * @SuppressWarnings(PHPMD.ShortVariable)
*/
final class UserInvitationDatabaseRepository implements UserInvitationRepository
{
    private Connection $db;

    private UserInvitationsTable $table;

    private UsersTable $userTable;

    /**
     * Constructor
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
     * @throws Database\DatabaseException
     * @throws NotFoundException
     */
    public function getByUniqueId(UniqueId $uuid) : Entity
    {
        $query = $this->createBaseQuery()
            ->where(field($this->table->column('uuid'))->eq(strval($uuid)))
            ->compile()
        ;

        $row = $this->db->execute($query)->fetch();
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
     * @throws Database\DatabaseException
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
        $this->db->execute($query);

        return new Entity(
            $this->db->lastInsertId(),
            $invitation
        );
    }

    /**
     * @inheritdoc
     * @throws Database\DatabaseException
     */
    public function update(Entity $invitation): void
    {
        $data = UserInvitationMapper::toPersistence($invitation->domain());
        $query = $this->db->createQueryFactory()
            ->update($this->table->from(), $data)
            ->where(field('id')->eq($invitation->id()))
            ->compile()
        ;
        $this->db->execute($query);
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
     * @throws Database\DatabaseException
     */
    public function getByEmail(EmailAddress $email): array
    {
        $query = $this->createBaseQuery()
            ->where(field($this->table->from() . '.email')->eq(strval($email)))
            ->compile()
        ;

        $rows = $this->db->execute($query)->fetchAll();
        return array_map(function ($row) {
            $invitationRow = $this->table->filter($row);
            $invitationRow->user = $this->userTable->filter($row);
            return UserInvitationMapper::toDomain($invitationRow);
        }, $rows);
    }
}
