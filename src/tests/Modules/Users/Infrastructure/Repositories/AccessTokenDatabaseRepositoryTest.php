<?php
/**
 * Testcase for Date
 */
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\EmailAddress;
use Kwai\Core\Domain\Timestamp;

use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;

require_once('Database.php');

/**
 * @group DB
 */
final class AccessTokenDatabaseRepositoryTest extends TestCase
{
    private $user;

    private $repo;

    public function setup() : void
    {
        $this->repo = new AccessTokenDatabaseRepository(\Database::getDatabase());
        $userRepo = new UserDatabaseRepository(\Database::getDatabase());
        $this->user = $userRepo->getByEmail(new EmailAddress($_ENV['user']));
    }

    public function testCreateAccessToken()
    {
        $future = new \DateTime('now +2 hours');
        $tokenIdentifier = new TokenIdentifier();
        $accessToken = new AccessToken((object) [
            'identifier' => $tokenIdentifier,
            'expiration' => Timestamp::createFromDateTime($future)
        ]);
        $accessToken->attachUser($this->user);
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
     */
    public function testGetByTokenIdentifier($tokenIdentifier): void
    {
        $accessToken = $this->repo->getByTokenIdentifier(
            $tokenIdentifier
        );
        $this->assertInstanceOf(
            Entity::class,
            $accessToken
        );
    }
}
