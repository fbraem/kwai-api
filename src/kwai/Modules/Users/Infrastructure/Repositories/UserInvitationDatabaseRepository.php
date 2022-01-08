<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserInvitationNotFoundException;
use Kwai\Modules\Users\Domain\UserInvitation;
use Kwai\Modules\Users\Infrastructure\Mappers\UserInvitationDTO;
use Kwai\Modules\Users\Infrastructure\Tables;
use Kwai\Modules\Users\Repositories\UserInvitationQuery;
use Kwai\Modules\Users\Repositories\UserInvitationRepository;
use function Latitude\QueryBuilder\field;

/**
 * Class UserInvitationDatabaseRepository
 *
 * UserInvitation Repository for read/write UserInvitation entity
 * from/to a database.
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class UserInvitationDatabaseRepository extends DatabaseRepository implements UserInvitationRepository
{
    public function __construct(Connection $db)
    {
        parent::__construct(
            $db,
            fn($item) => UserInvitationDTO::toDomain($item)
        );
    }
    /**
     * @inheritdoc
     */
    public function getByUniqueId(UniqueId $uuid) : Entity
    {
        $query = $this->createQuery()
            ->filterByUniqueId($uuid)
        ;

        $invitations = $this->getAll($query);
        if ($invitations->isEmpty()) {
            throw new UserInvitationNotFoundException($uuid);
        }

        return $invitations->first();
    }

    /**
     * @inheritdoc
     */
    public function create(UserInvitation $invitation): Entity
    {
        $data = UserInvitationDTO::toPersistence($invitation);

        $query = $this->db->createQueryFactory()
            ->insert(Tables::USER_INVITATIONS->value)
            ->columns(
                ... $data->keys()
            )
            ->values(
                ... $data->values()
            )
        ;
        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
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
        $invitation->getTraceableTime()->markUpdated();
        $data = UserInvitationDTO::toPersistence($invitation->domain());
        $query = $this->db->createQueryFactory()
            ->update(
                Tables::USER_INVITATIONS->value,
                $data->toArray()
            )
            ->where(field('id')->eq($invitation->id()))
        ;
        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function createQuery(): UserInvitationQuery
    {
        return new UserInvitationDatabaseQuery($this->db);
    }
}
