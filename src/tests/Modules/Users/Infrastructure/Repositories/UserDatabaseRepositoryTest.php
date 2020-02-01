<?php
/**
 * Testcase for Date
 */
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Domain\EmailAddress;

use Kwai\Modules\Users\Infrastructure\UserTable;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;

require_once('Database.php');

/**
 * @group DB
 */
final class UserDatabaseRepositoryTest extends TestCase
{
    private $repo;

    public function setup(): void
    {
        $this->repo = new UserDatabaseRepository(\Database::getDatabase());
    }

    public function testGetByEmail(): Entity
    {
        $user = $this->repo->getByEmail(new EmailAddress('test@kwai.com'));
        $this->assertInstanceOf(
            Entity::class,
            $user
        );
        return $user;
    }

    /**
     * @depends testGetByEmail
     */
    public function testGetById(Entity $user): UniqueId
    {
        $entity = $this->repo->getById($user->id());
        $this->assertInstanceOf(
            Entity::class,
            $entity
        );
        return $entity->getUuid();
    }

    /**
     * @depends testGetById
     */
    public function testGetByUuid(UniqueId $uuid): void
    {
        $user = $this->repo->getByUuid($uuid);
        $this->assertInstanceOf(
            Entity::class,
            $user
        );
    }

    public function testGetByIdNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $user = $this->repo->getById(10000);
    }

    /**
     * @depends testGetByEmail
     */
    public function testGetByAccessToken(Entity $user): void
    {
        $accessTokenRepo = new AccessTokenDatabaseRepository(\Database::getDatabase());
        $accessTokens = $accessTokenRepo->getTokensForUser($user);
        if (count($accessTokens) > 0) {
            $repo = new UserDatabaseRepository(\Database::getDatabase());
            $user = $this->repo->getByAccessToken(
                $accessTokens[0]->getIdentifier()
            );
            $this->assertInstanceOf(
                Entity::class,
                $user
            );
        }
    }
}
