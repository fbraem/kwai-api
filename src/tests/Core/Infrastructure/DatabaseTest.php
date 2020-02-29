<?php
/**
 * Testcase for Database
 */
declare(strict_types=1);

namespace Tests\Core\Domain\Infrastructure;

use PHPUnit\Framework\TestCase;

use Kwai\Core\Infrastructure\Database;

use Latitude\QueryBuilder\QueryFactory;

final class DatabaseTest extends TestCase
{
    private const DB_MEMORY = 'sqlite::memory:';

    public function testDatabase(): void
    {
        $this->assertInstanceOf(
            Database\Connection::class,
            new Database\Connection(self::DB_MEMORY)
        );
    }

    public function testDatabaseException(): void
    {
        $this->expectException(Database\DatabaseException::class);
        $db = new Database\Connection('sqlite');
    }

    public function testDatabaseQueryFactory(): void
    {
        $db = new Database\Connection(self::DB_MEMORY);
        $qf = $db->createQueryFactory();
        $this->assertInstanceOf(
            QueryFactory::class,
            $qf
        );
    }

    public function testDatabaseQuery(): void
    {
        $db = new Database\Connection(self::DB_MEMORY);
        $qf = $db->createQueryFactory();
        $query = $qf
            ->select('name', 'type')
            ->from('sqlite_master')
            ->compile()
        ;
        $stmt = $db->execute($query);
        $result = $stmt->fetchAll();
        $this->assertIsArray($result);
    }

    public function testDatabaseQueryException(): void
    {
        $this->expectException(Database\DatabaseException::class);

        $db = new Database\Connection(self::DB_MEMORY);
        $qf = $db->createQueryFactory();
        $query = $qf
            ->select()
            ->from('')
            ->compile()
        ;
        $stmt = $db->execute($query);
    }
}
