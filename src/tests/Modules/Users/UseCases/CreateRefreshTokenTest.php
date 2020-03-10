<?php
/**
 * Testcase for CreateRefreshToken
 */
declare(strict_types=1);

namespace Tests\Modules\Users\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Modules\Users\Domain\Exceptions\AuthenticationException;
use Kwai\Modules\Users\Domain\RefreshToken;
use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RefreshTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\UseCases\AuthenticateUser;
use Kwai\Modules\Users\UseCases\AuthenticateUserCommand;
use Kwai\Modules\Users\UseCases\CreateRefreshToken;
use Kwai\Modules\Users\UseCases\CreateRefreshTokenCommand;
use Tests\DatabaseTestCase;

/**
 * @group DB
 */
final class CreateRefreshTokenTest extends DatabaseTestCase
{
    public function testAuthenticate(): void
    {
        $command = new AuthenticateUserCommand([
            'email' => $_ENV['user'],
            'password' => $_ENV['password']
        ]);

        $refreshTokenRepo = new RefreshTokenDatabaseRepository(self::$db);
        $accessTokenRepo = new AccessTokenDatabaseRepository(self::$db);

        try {
            $refreshToken = (new AuthenticateUser(
                new UserDatabaseRepository(self::$db),
                $accessTokenRepo,
                $refreshTokenRepo
            ))($command);

            /** @noinspection PhpUndefinedMethodInspection */
            $command = new CreateRefreshTokenCommand([
                'refresh_token_identifier' => strval($refreshToken->getIdentifier())
            ]);

            $refreshToken = (new CreateRefreshToken(
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

        } catch (NotFoundException $e) {
            $this->assertTrue(false, $e->getMessage());
        } catch (AuthenticationException $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }
}
