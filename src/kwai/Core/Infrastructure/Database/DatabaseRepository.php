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

        return $rows->mapWithKeys(
            fn($item, $key) => [
                $key => new Entity((int) $key, ($this->mapFunction)($item))
            ]
        );
    }
}
