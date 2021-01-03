<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Domain\Exceptions\AuthorNotFoundException;
use Kwai\Modules\News\Infrastructure\Mappers\AuthorMapper;
use Kwai\Modules\News\Repositories\AuthorQuery;
use Kwai\Modules\News\Repositories\AuthorRepository;

/**
 * Class AuthorDatabaseRepository
 */
class AuthorDatabaseRepository extends DatabaseRepository implements AuthorRepository
{
    /**
     * @inheritDoc
     */
    private function createQuery(): AuthorQuery
    {
        return new AuthorDatabaseQuery($this->db);
    }

    /**
     * @inheritDoc
     */
    public function getAll(
        ?AuthorQuery $query = null,
        ?int $limit = null,
        ?int $offset = null
    ): Collection {
        $query ??= $this->createQuery();

        $authors = $query->execute($limit, $offset);
        return $authors->mapWithKeys(
            fn($item, $key) => [
                $key => new Entity((int) $key, AuthorMapper::toDomain($item))
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): Entity
    {
        $query = $this->createQuery()->filterIds($id);

        try {
            $entities = $this->getAll($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        if ($entities->isEmpty()) {
            throw new AuthorNotFoundException($id);
        }

        return $entities->first();
    }

    /**
     * @inheritDoc
     */
    public function getByUniqueId(UniqueId $uuid): Entity
    {
        $query = $this->createQuery()->filterUniqueId($uuid);

        try {
            $entities = $this->getAll($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        if ($entities->isEmpty()) {
            throw new AuthorNotFoundException($uuid);
        }

        return $entities->first();
    }
}
