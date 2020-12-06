<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Exceptions\DefinitionNotFoundException;
use Kwai\Modules\Trainings\Infrastructure\Mappers\DefinitionMapper;
use Kwai\Modules\Trainings\Repositories\DefinitionQuery;
use Kwai\Modules\Trainings\Repositories\DefinitionRepository;

/**
 * Class DefinitionDatabaseRepository
 */
class DefinitionDatabaseRepository extends DatabaseRepository implements DefinitionRepository
{
    /**
     * @inheritDoc
     */
    public function getById(int $id): Entity
    {
        $query = $this->createQuery();
        $query->filterId($id);

        try {
            $entities = $this->getAll($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        if ($entities->count() > 0) {
            return $entities->get($id);
        }

        throw new DefinitionNotFoundException($id);
    }

    /**
     * @inheritDoc
     */
    public function createQuery(): DefinitionQuery
    {
        return new DefinitionDatabaseQuery($this->db);
    }

    public function getAll(DefinitionQuery $query, ?int $limit = null, ?int $offset = null): Collection
    {
        /* @var Collection $definitions */
        $definitions = $query->execute($limit, $offset);
        return $definitions->mapWithKeys(
            fn($item, $key) => [ $key => DefinitionMapper::toDomain($item) ]
        );
    }
}
