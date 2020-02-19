<?php
/**
 * Testcase for TokenIdentifier
 */
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Kwai\Core\Domain\Entity;

use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RefreshTokenDatabaseRepository;
use Kwai\Modules\Users\UseCases\AuthenticateUser;
use Kwai\Modules\Users\UseCases\AuthenticateUserCommand;
use Kwai\Modules\Users\UseCases\Logout;
use Kwai\Modules\Users\UseCases\LogoutCommand;
use Kwai\Modules\Users\Domain\RefreshToken;

require_once('Database.php');

/**
 * @group DB
 */
final class LogoutTest extends TestCase
{
    public function testAuthenticate(): void
    {
        $command = new AuthenticateUserCommand([
            'email' => $_ENV['user'],
            'password' => $_ENV['password']
        ]);

        $refreshTokenRepo = new RefreshTokenDatabaseRepository(\Database::getDatabase());
        $accessTokenRepo = new AccessTokenDatabaseRepository(\Database::getDatabase());

        $refreshToken = (new AuthenticateUser(
            new UserDatabaseRepository(\Database::getDatabase()),
            $accessTokenRepo,
            $refreshTokenRepo
        ))($command);

        $command = new LogoutCommand([
            'refresh_token_identifier' => strval($refreshToken->getIdentifier())
        ]);
        (new Logout(
            $refreshTokenRepo,
            $accessTokenRepo
        ))($command);

        $this->assertInstanceOf(
            Entity::class,
            $refreshToken
        );
        $this->assertInstanceOf(
            RefreshToken::class,
            $refreshToken->domain()
        );
    }
}