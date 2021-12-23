<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Database;

use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Kwai\Core\Infrastructure\Repositories\Query;
use Latitude\QueryBuilder\Query\SelectQuery;
use function Latitude\QueryBuilder\alias;
use function Latitude\QueryBuilder\func;
use function Latitude\QueryBuilder\literal;

/**
 * Class DatabaseQuery
 *
 * Base class for database select queries.
 */
abstract class DatabaseQuery implements Query
{
    /**
     * A connection to the database
     */
    protected Connection $db;

    /**
     * The query
     */
    protected SelectQuery $query;

    private string $countColumn;

    /**
     * DatabaseQuery constructor.
     *
     * Set count column when joins are used that result in multiple rows
     * with the same id. This will result in a count(distinct(column)) query.
     *
     * @param Connection $db
     * @param string     $countColumn
     */
    public function __construct(Connection $db, string $countColumn = '0')
    {
        $this->db = $db;
        $this->query = $this->db->createQueryFactory()->select();
        $this->countColumn = $countColumn;
        $this->initQuery();
    }

    /**
     * Initialize the query.
     * Add the tables to select and to join for this query.
     */
    abstract protected function initQuery(): void;

    /**
     * Return all columns used for the select query.
     * @return array
     */
    abstract protected function getColumns(): array;

    /**
     * @inheritDoc
     */
    public function count(): int
    {
        // Reset limit/offset to avoid a wrong result
        $this->query->limit(null);
        $this->query->offset(null);

        // Instead of trying to count a column, we just
        // count the number '0'
        if ($this->countColumn == '0') {
            $this->query->columns(
                alias(func('COUNT', literal('0')), 'c')
            );
        } else {
            $this->query->columns(
                alias(
                    func(
                        'COUNT',
                        func('distinct', $this->countColumn)
                    ),
                    'c'
                )
            );
        }

        $row = $this->db->execute(
            $this->query
        )->fetch();
        return (int) $row['c'];
    }

    /**
     * Default implementation: return all records.
     * @inheritDoc
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $this->query->limit($limit);
        $this->query->offset($offset);

        $this->query->columns(
            ... $this->getColumns()
        );

        /** @noinspection PhpUndefinedMethodInspection */
        return collect($this->db->execute($this->query)->fetchAll())
            ->recursive();
    }

    /**
     * Does the same as execute, except it allows to lazy load the
     * records.
     *
     * @param int|null $limit
     * @param int|null $offset
     * @return LazyCollection
     * @throws QueryException
     */
    protected function walk(?int $limit = null, ?int $offset = null): LazyCollection
    {
        $this->query->limit($limit);
        $this->query->offset($offset);

        $this->query->columns(
            ... $this->getColumns()
        );

        return LazyCollection::make($this->db->walk($this->query));
    }
}
