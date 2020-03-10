<?php
/**
 * Testcase for Date
 */
declare(strict_types=1);

namespace Tests\Modules\Users\Infrastructure\Repositories;

use DateTime;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Modules\Users\Repositories\RefreshTokenRepository;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\EmailAddress;
use Kwai\Core\Domain\Timestamp;

use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\RefreshTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Domain\RefreshToken;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Tests\DatabaseTestCase;

/**
 * @group DB
 */
final class RefreshTokenDatabaseRepositoryTest extends DatabaseTestCase
{
    /**
     * @var Entity<User>
     */
    private Entity $user;

    private RefreshTokenRepository $repo;

    public function setup() : void
    {
        $this->repo = new RefreshTokenDatabaseRepository(self::$db);
        $userRepo = new UserDatabaseRepository(self::$db);
        try {
            $this->user = $userRepo->getByEmail(new EmailAddress($_ENV['user']));
        } catch (NotFoundException $e) {
            echo $e->getMessage(), PHP_EOL;
        }
    }

    public function testCreateRefreshToken(): TokenIdentifier
    {
        $accessTokenRepo = new AccessTokenDatabaseRepository(self::$db);
        $future = new DateTime('now +2 hours');
        $tokenIdentifier = new TokenIdentifier();
        $accessToken = new AccessToken((object) [
            'identifier' => $tokenIdentifier,
            'expiration' => Timestamp::createFromDateTime($future),
            'account' => $this->user
        ]);
        $accessTokenEntity = $accessTokenRepo->create($accessToken);

        $tokenIdentifier = new TokenIdentifier();
        $refreshToken = new RefreshToken((object) [
            'identifier' => $tokenIdentifier,
            'expiration' => Timestamp::createFromDateTime($future),
            'accessToken' => $accessTokenEntity
        ]);
        $entity = $this->repo->create($refreshToken);
        $this->assertInstanceOf(
            Entity::class,
            $entity
        );
        return $tokenIdentifier;
    }

    /**
     * @depends testCreateRefreshToken
     * @param TokenIdentifier $tokenIdentifier
     */
    public function testGetByTokenIdentifier(TokenIdentifier $tokenIdentifier): void
    {
        try {
            $refreshToken = $this->repo->getByTokenIdentifier(
                $tokenIdentifier
            );
            $this->assertInstanceOf(
                Entity::class,
                $refreshToken
            );
        } catch (NotFoundException $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }
}
