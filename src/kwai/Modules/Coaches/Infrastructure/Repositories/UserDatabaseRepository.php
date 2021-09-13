<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Repositories\Query;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Coaches\Domain\Exceptions\CoachNotFoundException;
use Kwai\Modules\Coaches\Domain\Exceptions\UserNotFoundException;
use Kwai\Modules\Coaches\Infrastructure\Mappers\UserMapper;
use Kwai\Modules\Coaches\Repositories\UserRepository;

/**
 * Class UserDatabaseRepository
 */
class UserDatabaseRepository extends DatabaseRepository implements UserRepository
{
    public function __construct(Connection $db)
    {
        parent::__construct(
            $db,
        fn($item) => UserMapper::toDomain($item)
        );
    }

    /**
     * @inheritDoc
     */
    public function createQuery(): UserDatabaseQuery
    {
        return new UserDatabaseQuery($this->db);
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): Entity
    {
        $query = $this->createQuery()->filterId($id);
        $users = $this->getAll($query);
        if ($users->isEmpty()) {
            throw new UserNotFoundException($id);
        }

        return $users->first();
    }
}
