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
use Kwai\Modules\Applications\Infrastructure\Tables;
use Kwai\Modules\Applications\Repositories\ApplicationQuery;
use Kwai\Modules\Applications\Repositories\ApplicationRepository;
use function Latitude\QueryBuilder\field;

/**
 * Class ApplicationDatabaseRepository
 */
class ApplicationDatabaseRepository extends DatabaseRepository implements ApplicationRepository
{
    public function __construct(Connection $db)
    {
        parent::__construct(
            $db,
            fn ($item) => ApplicationMapper::toDomain($item)
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

    /**
     * @inheritDoc
     */
    public function update(Entity $application): void
    {
        $data = ApplicationMapper::toPersistence($application->domain());

        $queryFactory = $this->db->createQueryFactory();

        $this->db->execute(
            $queryFactory
                ->update((string) Tables::APPLICATIONS())
                ->set($data->toArray())
                ->where(field('id')->eq($application->id()))
        );
    }
}
