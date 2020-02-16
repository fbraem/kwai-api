<?php
/**
 * Testcase for Database
 */
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Kwai\Core\Infrastructure\Database;
use Kwai\Core\Infrastructure\Exceptions\DatabaseException;

use Latitude\QueryBuilder\QueryFactory;

final class DatabaseTest extends TestCase
{
    public function testDatabase(): void
    {
        $this->assertInstanceOf(
            Database::class,
            new Database('sqlite::memory:')
        );
    }

    public function testDatabaseException(): void
    {
        $this->expectException(DatabaseException::class);
        $db = new Database('sqlite');
    }

    public function testDatabaseQueryFactory(): void
    {
        $db = new Database('sqlite::memory:');
        $qf = $db->createQueryFactory();
        $this->assertInstanceOf(
            QueryFactory::class,
            $qf
        );
    }

    public function testDatabaseQuery(): void
    {
        $db = new Database('sqlite::memory:');
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
        $this->expectException(DatabaseException::class);

        $db = new Database('sqlite::memory');
        $qf = $db->createQueryFactory();
        $query = $qf
            ->select()
            ->from('')
            ->compile()
        ;
        $stmt = $db->execute($query);
    }
}
