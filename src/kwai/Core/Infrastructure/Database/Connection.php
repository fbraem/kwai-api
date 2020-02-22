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

/**
 * A class that represents a database connection
 */
final class Connection
{
    /**
     * A PDO connection
     * @var \PDO
     */
    private $pdo;

    /**
     * Constructor.
     * @param string $dsn        A DSN connection.
     * @param string $user       A username
     * @param string $password   A password
     * @throws DatabaseException Thrown when connection failed
     */
    public function __construct(
        string $dsn,
        string $user = '',
        string $password = ''
    ) {
        try {
            $this->pdo = new \PDO(
                $dsn,
                $user,
                $password,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_PERSISTENT => true, // BEST OPTION
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ
                ]
            );
        } catch (\PDOException $e) {
            throw new DatabaseException($e);
        }
    }

    /**
     * Create a query factory.
     * @return QueryFactory
     */
    public function createQueryFactory(): QueryFactory
    {
        $driver = $this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
        if ($driver == 'mysql') {
            return new QueryFactory(new MySqlEngine());
        }
        return new QueryFactory(new CommonEngine());
    }

    /**
     * Execute the query and returns a PDOStatement on success.
     * @param  Query         $query The query to execute
     * @return \PDOStatement        The executed statement
     * @throws DatabaseException    Thrown when a PDOException occurred
     */
    public function execute(Query $query): \PDOStatement
    {
        try {
            $stmt = $this->pdo->prepare($query->sql());
            $stmt->execute($query->params());
            return $stmt;
        } catch (\PDOException $e) {
            throw new DatabaseException($e, $query->sql());
        }
    }

    /**
     * Returns the last inserted id
     * @return int
     */
    public function lastInsertId(): int
    {
        return (int) $this->pdo->lastInsertId();
    }
}
