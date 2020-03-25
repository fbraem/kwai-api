<?php
/**
 * Testcase for Logout
 */
declare(strict_types=1);

namespace Tests\Modules\Users\UseCases;

use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\AuthenticationException;

use Kwai\Core\Domain\Entity;

use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RefreshTokenDatabaseRepository;
use Kwai\Modules\Users\UseCases\AuthenticateUser;
use Kwai\Modules\Users\UseCases\AuthenticateUserCommand;
use Kwai\Modules\Users\UseCases\Logout;
use Kwai\Modules\Users\UseCases\LogoutCommand;
use Kwai\Modules\Users\Domain\RefreshToken;
use Tests\DatabaseTestCase;

/**
 * @group DB
 */
final class LogoutTest extends DatabaseTestCase
{
    public function testAuthenticate(): void
    {
        $command = new AuthenticateUserCommand();
        $command->email = $_ENV['user'];
        $command->password = $_ENV['password'];

        $refreshTokenRepo = new RefreshTokenDatabaseRepository(self::$db);
        $accessTokenRepo = new AccessTokenDatabaseRepository(self::$db);

        try {
            $refreshToken = (new AuthenticateUser(
                new UserDatabaseRepository(self::$db),
                $accessTokenRepo,
                $refreshTokenRepo
            ))($command);
            $this->assertInstanceOf(
                Entity::class,
                $refreshToken
            );
            $this->assertInstanceOf(
                RefreshToken::class,
                $refreshToken->domain()
            );

            $command = new LogoutCommand();
            /** @noinspection PhpUndefinedMethodInspection */
            $command->identifier = strval($refreshToken->getIdentifier());

            (new Logout(
                $refreshTokenRepo,
                $accessTokenRepo
            ))($command);
        } catch (NotFoundException $e) {
            self::assertTrue(false, $e->getMessage());
        } catch (AuthenticationException $e) {
            self::assertTrue(false, $e->getMessage());
        } catch (RepositoryException $e) {
            self::assertTrue(false, $e->getMessage());
        }
    }
}
