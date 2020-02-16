<?php
/**
 * @package kwai
 * @subpackage Auth
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Infrastructure\Database;

use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\UserInvitation;
use Kwai\Modules\Users\Infrastructure\Mappers\UserInvitationMapper;
use Kwai\Modules\Users\Infrastructure\UserInvitationTable;
use Kwai\Modules\Users\Infrastructure\UserTable;

use Kwai\Modules\Users\Repositories\UserInvitationRepository;

use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\alias;
use function Latitude\QueryBuilder\on;
use Latitude\QueryBuilder\Query\SelectQuery;

/**
* UserInvitation Repository for read/write UserInvitation entity
* from/to a database.
*/
final class UserInvitationDatabaseRepository implements UserInvitationRepository
{
    /**
     * @var Database
     */
    private $db;

    /**
     * UserInvitation table
     * @var UserInvitationTable
     */
    private $table;

    /**
     * User table
     * @var UserTable
     */
    private $userTable;

    /**
     * Constructor
     *
     * @param Database $db A database object
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
        $this->table = new UserInvitationTable();
        $this->userTable = new UserTable();
    }

    /**
     * @inheritdoc
     */
    public function getByUniqueId(UniqueId $uuid) : Entity
    {
        $query = $this->createBaseQuery()
            ->where(field('token')->eq(strval($uuid)))
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
     */
    public function create(UserInvitation $invitation): Entity
    {
        $data = UserInvitation::toPersistence($invitation);

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
        $stmt = $this->db->execute($query);

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
        $data = UserInvitationMapper::toPersistence($invitation->domain());
        $query = $this->db->createQueryFactory()
            ->update($this->table->from(), $data)
            ->where(field('id')->eq($invitation->id()))
            ->compile()
        ;
        $stmt = $this->db->execute($query);
    }

    /**
     * Create the base SELECT query
     * @return SelectQuery
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
}
