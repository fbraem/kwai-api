<?php
/**
 * Testcase for TokenIdentifier
 */
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Kwai\Core\Domain\Entity;

use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\UseCases\AuthenticateUser;
use Kwai\Modules\Users\UseCases\AuthenticateUserCommand;

require_once('Database.php');

/**
 * @group DB
 */
final class AuthenticateUserTest extends TestCase
{
    public function testAuthenticate(): void
    {
        $command = new AuthenticateUserCommand([
            'email' => $_ENV['user'],
            'password' => $_ENV['password']
        ]);

        $accessToken = (new AuthenticateUser(
            new UserDatabaseRepository(\Database::getDatabase()),
            new AccessTokenDatabaseRepository(\Database::getDatabase())
        ))($command);

        $this->assertInstanceOf(
            Entity::class,
            $accessToken
        );
    }
}
