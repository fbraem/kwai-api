<?php
/**
 * @package Modules
 * @subpackage Pages
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Modules\Pages\Domain\Exceptions\ApplicationNotFoundException;
use Kwai\Modules\Pages\Infrastructure\Mappers\ApplicationMapper;
use Kwai\Modules\Pages\Repositories\ApplicationQuery;
use Kwai\Modules\Pages\Repositories\ApplicationRepository;

/**
 * Class ApplicationDatabaseRepository
 */
class ApplicationDatabaseRepository extends DatabaseRepository implements ApplicationRepository
{
    /**
     * ApplicationDatabaseRepository constructor.
     *
     * @param Connection $db
     */
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
        if ($entities->count() === 0) {
            throw new ApplicationNotFoundException($id);
        }

        return $entities->first();
    }

    /**
     * @return ApplicationQuery
     */
    public function createQuery(): ApplicationQuery
    {
        return new ApplicationDatabaseQuery($this->db);
    }
}
