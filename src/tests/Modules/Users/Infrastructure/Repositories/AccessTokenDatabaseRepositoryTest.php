<?php
/**
 * Testcase for Date
 */
declare(strict_types=1);

namespace Tests\Modules\Users\Infrastructure\Repositories;

use DateTime;
use Kwai\Core\Domain\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\Timestamp;
use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Repositories\AccessTokenRepository;
use Tests\DatabaseTestCase;

/**
 * @group DB
 */
final class AccessTokenDatabaseRepositoryTest extends DatabaseTestCase
{
    /**
     * @var Entity<User>
     */
    private Entity $user;

    private AccessTokenRepository $repo;

    public function setup() : void
    {
        $this->repo = new AccessTokenDatabaseRepository(self::getDatabase());
        $userRepo = new UserDatabaseRepository(self::getDatabase());
        try {
            $this->user = $userRepo->getAccount(new EmailAddress($_ENV['user']));
        } catch (NotFoundException $e) {
            echo $e->getMessage(), PHP_EOL;
        }
    }

    public function testCreateAccessToken()
    {
        $future = new DateTime('now +2 hours');
        $tokenIdentifier = new TokenIdentifier();
        $accessToken = new AccessToken((object) [
            'identifier' => $tokenIdentifier,
            'expiration' => Timestamp::createFromDateTime($future),
            'account' => $this->user
        ]);
        $entity = $this->repo->create($accessToken);
        $this->assertInstanceOf(
            Entity::class,
            $entity
        );
        return $tokenIdentifier;
    }

    /**
     * @depends testCreateAccessToken
     */
    public function testGetTokensForUser(): void
    {
        $accessTokens = $this->repo->getTokensForUser($this->user);
        $this->assertContainsOnlyInstancesOf(
            Entity::class,
            $accessTokens
        );
    }

    /**
     * @depends testCreateAccessToken
     * @param TokenIdentifier $tokenIdentifier
     */
    public function testGetByTokenIdentifier(TokenIdentifier $tokenIdentifier): void
    {
        try {
            $accessToken = $this->repo->getByTokenIdentifier(
                $tokenIdentifier
            );
            $this->assertInstanceOf(
                Entity::class,
                $accessToken
            );
        } catch (NotFoundException $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }
}
