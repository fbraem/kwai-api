<?php
/**
 * @package Kwai
 * @subpackage Core
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Database;

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

    /**
     * DatabaseQuery constructor.
     *
     * @param Connection  $db
     */
    public function __construct(Connection $db)
    {
        $this->db = $db;
        $this->query = $this->db->createQueryFactory()->select();
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
        $this->query->columns(
            alias(func('COUNT', literal('0')), 'c')
        );

        $row = $this->db->execute(
            $this->query
        )->fetch();
        return (int) $row->c;
    }

    /**
     * @inheritDoc
     */
    public function execute(?int $limit = null, ?int $offset = null)
    {
        $this->query->limit($limit);
        $this->query->offset($offset);

        $this->query->columns(
            ... $this->getColumns()
        );

        return $this->db->execute($this->query)->fetchAll();
    }
}
