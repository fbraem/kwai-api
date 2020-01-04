<?php
/**
 * Testcase for Date
 */
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Opis\Database\Database;
use Opis\Database\Connection;

use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Domain\User;

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
        $connection->option(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        self::$db = new Database($connection);
    }

    public function testGetById(): void
    {
        $repo = new UserDatabaseRepository(self::$db);
        $user = $repo->getById(1);
        $this->assertInstanceOf(
            User::class,
            $user
        );
    }
}
