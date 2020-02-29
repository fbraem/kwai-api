<?php
/**
 * Testcase for AuthenticationUser
 */
declare(strict_types=1);

namespace Tests\Modules\Users\UseCases;

use Kwai\Core\Domain\Entity;

use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RefreshTokenDatabaseRepository;
use Kwai\Modules\Users\UseCases\AuthenticateUser;
use Kwai\Modules\Users\UseCases\AuthenticateUserCommand;
use Kwai\Modules\Users\Domain\RefreshToken;
use Kwai\Modules\Users\Domain\Exceptions\AuthenticationException;
use Tests\DatabaseTestCase;

/**
 * @group DB
 */
final class AuthenticateUserTest extends DatabaseTestCase
{
    public function testAuthenticate(): void
    {
        $command = new AuthenticateUserCommand([
            'email' => $_ENV['user'],
            'password' => $_ENV['password']
        ]);

        try {
            $refreshToken = (new AuthenticateUser(
                new UserDatabaseRepository(self::getDatabase()),
                new AccessTokenDatabaseRepository(self::getDatabase()),
                new RefreshTokenDatabaseRepository(self::getDatabase())
            ))($command);
        } catch (NotFoundException $e) {
        } catch (AuthenticationException $e) {
        }

        $this->assertInstanceOf(
            Entity::class,
            $refreshToken
        );
        $this->assertInstanceOf(
            RefreshToken::class,
            $refreshToken->domain()
        );
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function testAuthenticateFailure(): void
    {
        $this->expectException(AuthenticationException::class);

        $command = new AuthenticateUserCommand([
            'email' => $_ENV['user'],
            'password' => 'invalid'
        ]);

        $refreshToken = (new AuthenticateUser(
            new UserDatabaseRepository(self::getDatabase()),
            new AccessTokenDatabaseRepository(self::getDatabase()),
            new RefreshTokenDatabaseRepository(self::getDatabase())
        ))($command);
    }
}
