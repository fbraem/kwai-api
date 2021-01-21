<?php
/**
 * @package Modules
 * @subpackage Applications
 */
declare(strict_types=1);

namespace Kwai\Modules\Applications\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Modules\Applications\Domain\Exceptions\ApplicationNotFoundException;
use Kwai\Modules\Applications\Infrastructure\Mappers\ApplicationMapper;
use Kwai\Modules\Applications\Repositories\ApplicationQuery;
use Kwai\Modules\Applications\Repositories\ApplicationRepository;

/**
 * Class ApplicationDatabaseRepository
 */
class ApplicationDatabaseRepository extends DatabaseRepository implements ApplicationRepository
{
    public function __construct(Connection $db)
    {
        parent::__construct(
            $db,
            fn($item) => ApplicationMapper::toDomain($item)
        );
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): Entity
    {
        $query = $this->createQuery()->filterId($id);

        $entities = $this->getAll($query);
        if ($entities->isNotEmpty()) {
            return $entities->get($id);
        }

        throw new ApplicationNotFoundException($id);
    }

    /**
     * @inheritDoc
     */
    public function createQuery(): ApplicationQuery
    {
        return new ApplicationDatabaseQuery($this->db);
    }
}
