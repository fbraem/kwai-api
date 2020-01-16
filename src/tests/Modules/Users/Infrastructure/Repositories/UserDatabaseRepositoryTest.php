<?php
/**
 * Testcase for Date
 */
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Opis\Database\Database;
use Opis\Database\Connection;

use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\Entity;

use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;

final class UserDatabaseRepositoryTest extends TestCase
{
    private static $db;

    public static function setUpBeforeClass(): void
    {
        $config = include __DIR__ . '/../../../../../../api/config.php';
        $dbConfig = $config['database'][$config['default_database']];
        $connection = new Connection(
            $dbConfig['adapter'] . ':host=' . $dbConfig['host'] . ';dbname=' . $dbConfig['name'],
            $dbConfig['user'],
            $dbConfig['pass']
        );
        $connection->initCommand('SET NAMES UTF8');
        self::$db = new Database($connection);
    }

    public function testGetById(): void
    {
        $repo = new UserDatabaseRepository(self::$db);
        $user = $repo->getById(1);
        $this->assertInstanceOf(
            Entity::class,
            $user
        );
    }

    public function testGetByIdNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $repo = new UserDatabaseRepository(self::$db);
        $user = $repo->getById(10000);
    }

    public function testGetByAccessToken(): void
    {
        $repo = new UserDatabaseRepository(self::$db);
        $user = $repo->getByAccessToken(
            new TokenIdentifier('dc23ea481a27e4ec1bc6ea20923bf4eb7b10e63f7a5df74e1be486a74a46c8ed7944c7287d234895')
        );
        $this->assertInstanceOf(
            Entity::class,
            $user
        );
    }
}
