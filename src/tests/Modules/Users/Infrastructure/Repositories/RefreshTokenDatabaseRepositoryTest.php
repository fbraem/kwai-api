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
use Kwai\Modules\Users\Infrastructure\Repositories\RefreshTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Domain\RefreshToken;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;

require_once('Database.php');

/**
 * @group DB
 */
final class RefreshTokenDatabaseRepositoryTest extends TestCase
{
    private $user;

    private $accessToken;

    private $repo;

    public function setup() : void
    {
        $this->repo = new RefreshTokenDatabaseRepository(\Database::getDatabase());
        $userRepo = new UserDatabaseRepository(\Database::getDatabase());
        $this->user = $userRepo->getByEmail(new EmailAddress($_ENV['user']));
    }

    public function testCreateRefreshToken(): TokenIdentifier
    {
        $accessTokenRepo = new AccessTokenDatabaseRepository(\Database::getDatabase());
        $future = new \DateTime('now +2 hours');
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
     */
    public function testGetByTokenIdentifier($tokenIdentifier): void
    {
        $refreshToken = $this->repo->getByTokenIdentifier(
            $tokenIdentifier
        );
        $this->assertInstanceOf(
            Entity::class,
            $refreshToken
        );
    }
}
