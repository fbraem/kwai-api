<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserRecoveryNotFoundException;
use Kwai\Modules\Users\Domain\UserRecovery;
use Kwai\Modules\Users\Domain\UserRecoveryEntity;
use Kwai\Modules\Users\Infrastructure\Mappers\UserRecoveryDTO;
use Kwai\Modules\Users\Infrastructure\UserRecoveriesTable;
use Kwai\Modules\Users\Repositories\UserRecoveryQuery;
use Kwai\Modules\Users\Repositories\UserRecoveryRepository;

/**
 * Class UserRecoveryDatabaseRepository
 */
class UserRecoveryDatabaseRepository extends DatabaseRepository implements UserRecoveryRepository
{
    public function __construct(Connection $db)
    {
        parent::__construct(
            $db,
            fn(UserRecoveryDTO $item) => $item->createEntity()
        );
    }

    /**
     * @inheritdoc
     */
    public function getByUniqueId(UniqueId $uuid): UserRecoveryEntity
    {
        $query = $this->createQuery()
            ->filterByUUID($uuid)
        ;

        $userRecoveries = $this->getAll($query);
        if ($userRecoveries->isEmpty()) {
            throw new UserRecoveryNotFoundException($uuid);
        }

        return $userRecoveries->first();
    }

    public function createQuery(): UserRecoveryQuery
    {
        return new UserRecoveryDatabaseQuery($this->db);
    }

    /**
     * @throws RepositoryException
     */
    public function create(UserRecovery $recovery): UserRecoveryEntity
    {
        $data = (new UserRecoveryDTO())
            ->persist($recovery)
            ->userRecoveriesTable
            ->collect()
            ->forget('id')
        ;

        $query = $this->db->createQueryFactory()
            ->insert(UserRecoveriesTable::name())
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

        return new UserRecoveryEntity(
            $this->db->lastInsertId(),
            $recovery
        );
    }
}