<?php
/**
 * Database class
 * @package Kwai
 * @subpackage Core
 * @author Franky Braem
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure\Database;

use Generator;
use Illuminate\Support\Collection;
use Latitude\QueryBuilder\Engine\SqliteEngine;
use Latitude\QueryBuilder\Query\AbstractQuery;
use Latitude\QueryBuilder\QueryFactory;
use Latitude\QueryBuilder\Engine\CommonEngine;
use Latitude\QueryBuilder\Engine\MySqlEngine;
use PDO;
use PDOException;
use PDOStatement;
use Psr\Log\LoggerInterface;

/**
 * A class that represents a database connection
 */
final class Connection
{
    /**
     * A PDO connection
     * @var PDO
     */
    private PDO $pdo;

    private ?LoggerInterface $logger;

    /**
     * Constructor.
     *
     * @param string               $dsn      A DSN connection.
     * @param string               $user     A username
     * @param string               $password A password
     * @param LoggerInterface|null $logger
     * @throws DatabaseException Thrown when connection failed
     */
    public function __construct(
        string $dsn,
        string $user = '',
        string $password = '',
        ?LoggerInterface $logger = null
    ) {
        $this->logger = $logger;
        try {
            $this->pdo = new PDO(
                $dsn,
                $user,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_PERSISTENT => true, // BEST OPTION
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
                ]
            );
        } catch (PDOException $e) {
            throw new DatabaseException($e);
        }
    }

    /**
     * Change the fetch mode to array. Default is Object.
     */
    public function asArray(): void
    {
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    /**
     * Change the fetch mode to object.
     */
    public function asObject(): void
    {
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
    }

    /**
     * Begins a transaction
     * @return bool
     * @throws DatabaseException
     */
    public function begin(): bool
    {
        try {
            return $this->pdo->beginTransaction();
        } catch (PDOException $e) {
            throw new DatabaseException($e);
        }
    }

    /**
     * Commits a transaction
     * @return bool
     * @throws DatabaseException
     */
    public function commit(): bool
    {
        try {
            return $this->pdo->commit();
        } catch (PDOException $e) {
            throw new DatabaseException($e);
        }
    }

    /**
     * Create a query factory.
     * @return QueryFactory
     */
    public function createQueryFactory(): QueryFactory
    {
        $driver = $this->getDriver();
        if ($driver == 'mysql') {
            return new QueryFactory(new MySqlEngine());
        } elseif ($driver == 'sqlite') {
            return new QueryFactory(new SqliteEngine());
        }
        return new QueryFactory(new CommonEngine());
    }

    /**
     * Build the query and execute. On success returns a PDOStatement.
     *
     * @param AbstractQuery $query The query to execute
     * @return PDOStatement        The executed statement
     * @throws QueryException
     */
    public function execute(AbstractQuery $query): PDOStatement
    {
        $compiledQuery = $query->compile();
        if ($this->logger) {
            $this->logger->debug($compiledQuery->sql(), $compiledQuery->params());
        }
        try {
            $stmt = $this->pdo->prepare($compiledQuery->sql());
            $stmt->execute($compiledQuery->params());
            return $stmt;
        } catch (PDOException $e) {
            throw new QueryException($compiledQuery->sql(), $e);
        }
    }

    /**
     * Run a select statement and returns a generator.
     *
     * @param AbstractQuery $query
     * @return Generator
     * @throws QueryException
     */
    public function walk(AbstractQuery $query)
    {
        $compiledQuery = $query->compile();
        if ($this->logger) {
            $this->logger->debug($compiledQuery->sql(), $compiledQuery->params());
        }
        try {
            $stmt = $this->pdo->prepare($compiledQuery->sql());
            $stmt->execute($compiledQuery->params());
        } catch (PDOException $e) {
            throw new QueryException($compiledQuery->sql(), $e);
        }

        while ($record = $stmt->fetch()) {
            if (is_array($record)) {
                yield(new Collection($record));
            } else {
                yield($record);
            }
        }
    }

    /**
     * Get the internal PDO connection handle.
     * @return PDO
     */
    public function getPDO(): PDO
    {
        return $this->pdo;
    }

    /**
     * Get the driver name
     */
    public function getDriver(): string
    {
        return $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    /**
     * Checks if a transaction is currently active
     * @return bool
     */
    public function inTransaction(): bool
    {
        return $this->pdo->inTransaction();
    }

    /**
     * Returns the last inserted id
     * @return int
     */
    public function lastInsertId(): int
    {
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Rollbacks a transaction
     * @return bool
     * @throws DatabaseException
     */
    public function rollback(): bool
    {
        try {
            return $this->pdo->rollBack();
        } catch (PDOException $e) {
            throw new DatabaseException($e);
        }
    }
}
