<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Database;

use Closure;
use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\EntityTrait;
use Kwai\Core\Infrastructure\Repositories\Query;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;

/**
 * Class DatabaseRepository
 *
 * Base class for database repositories
 */
abstract class DatabaseRepository
{
    /**
     * DatabaseRepository constructor.
     *
     * @todo When all repositories have a mapper function, remove nullable
     * @param Connection   $db
     * @param Closure|null $mapFunction
     */
    public function __construct(
        protected Connection $db,
        private ?Closure $mapFunction = null
    ) {
    }

    /**
     * @return Query
     */
    abstract public function createQuery(): Query;

    /**
     * @param Query|null $query
     * @param int|null   $limit
     * @param int|null   $offset
     * @return Collection
     * @throws RepositoryException
     */
    public function getAll(?Query $query = null, ?int $limit = null, ?int $offset = null): Collection
    {
        $query ??= $this->createQuery();

        try {
            $rows = $query->execute($limit, $offset);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        // TODO: old mapper functions return the Domain object, some return
        //  an Entity. This can be refactored, when all entities use EntityTrait
        $mapFn = $this->mapFunction;
        if ($this->mapFunction) {
            return $rows->mapWithKeys(
                function ($item, $key) use ($mapFn) {
                    $result = $mapFn($item);
                    if (is_object($result) && isset(class_uses(get_class($result))[EntityTrait::class])) {
                        return [ $result->id() => $result ];
                    }
                    // TODO: the following code will be obsolete when EntityTrait will be used everywhere
                    if (is_a($result, Entity::class)) {
                        return [ $result->id() => $result ];
                    }
                    // TODO: the following code will be obsolete when EntityTrait will be used everywhere
                    return [ $key => new Entity((int) $key, $result) ];
                }
            );
        }

        return $rows;
    }
}
