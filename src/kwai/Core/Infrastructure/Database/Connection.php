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
use Kwai\Core\Infrastructure\Configuration\DsnDatabaseConfiguration;
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

    /**
     * Constructor.
     *
     * @param DsnDatabaseConfiguration $dsn A DSN connection.
     * @param LoggerInterface|null     $logger
     */
    public function __construct(
        private DsnDatabaseConfiguration $dsn,
        private ?LoggerInterface $logger = null
    ) {
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
     * Connects to the database. On failure a DatabaseException is thrown.
     *
     * @param string|null $user
     * @param string|null $password
     * @throws DatabaseException
     */
    public function connect(?string $user = '', ?string $password = ''): void
    {
        try {
            $this->pdo = new PDO(
                (string) $this->dsn,
                $user,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_PERSISTENT => true, // BEST OPTION
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
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
     * Get the database name
     */
    public function getDatabaseName(): string
    {
        return $this->dsn->getDatabaseName();
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
