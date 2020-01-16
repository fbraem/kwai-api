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

use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;

final class AccessTokenDatabaseRepositoryTest extends TestCase
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

    public function testGetTokensForUser(): void
    {
        $repo = new UserDatabaseRepository(self::$db);
        $user = $repo->getById(1);

        $repo = new AccessTokenDatabaseRepository(self::$db);
        $accessTokens = $repo->getTokensForUser($user);
        $this->assertContainsOnlyInstancesOf(
            Entity::class,
            $accessTokens
        );
    }

    public function testGetByTokenIdentifier(): void
    {
        //TODO: Make sure the token is there ...
        $repo = new AccessTokenDatabaseRepository(self::$db);
        $accessToken = $repo->getByTokenIdentifier(
            new TokenIdentifier('dc23ea481a27e4ec1bc6ea20923bf4eb7b10e63f7a5df74e1be486a74a46c8ed7944c7287d234895')
        );
        $this->assertInstanceOf(
            Entity::class,
            $accessToken
        );
    }
}
