<?php
/**
 * Database class
 * @package Kwai
 * @subpackage Core
 * @author Franky Braem
 */
declare(strict_types = 1);

namespace Kwai\Core\Infrastructure\Database;

use Latitude\QueryBuilder\QueryFactory;
use Latitude\QueryBuilder\Query;

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
        $driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        if ($driver == 'mysql') {
            return new QueryFactory(new MySqlEngine());
        }
        return new QueryFactory(new CommonEngine());
    }

    /**
     * Execute the query and returns a PDOStatement on success.
     * @param  Query         $query The query to execute
     * @return PDOStatement         The executed statement
     * @throws DatabaseException    Thrown when a PDOException occurred
     */
    public function execute(Query $query): PDOStatement
    {
        if ($this->logger) {
            $this->logger->debug($query->sql(), $query->params());
        }
        try {
            $stmt = $this->pdo->prepare($query->sql());
            $stmt->execute($query->params());
            return $stmt;
        } catch (PDOException $e) {
            throw new DatabaseException($e, $query->sql());
        }
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
