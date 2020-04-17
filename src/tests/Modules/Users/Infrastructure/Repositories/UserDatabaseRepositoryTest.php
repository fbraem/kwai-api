<?php
/**
 * Testcase for Date
 */
declare(strict_types=1);

namespace Tests\Modules\Users\Infrastructure\Repositories;

use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Infrastructure\Repositories\AccessTokenDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Repositories\UserRepository;
use Tests\DatabaseTestCase;

/**
 * @group DB
 */
final class UserDatabaseRepositoryTest extends DatabaseTestCase
{
    private UserRepository $repo;

    public function setup(): void
    {
        $this->repo = new UserDatabaseRepository(self::$db);
    }

    /**
     * @return Entity<User>
     */
    public function testGetByEmail(): Entity
    {
        try {
            $user = $this->repo->getByEmail(new EmailAddress('test@kwai.com'));
            $this->assertInstanceOf(
                Entity::class,
                $user
            );
            return $user;
        } catch (NotFoundException $e) {
            $this->assertTrue(false, $e->getMessage());
        }
        return null;
    }

    public function testExistsWithEmail(): void
    {
        $exist = $this->repo->existsWithEmail(new EmailAddress('test@kwai.com'));
        $this->assertTrue($exist);
    }

    public function testNotExistsWithEmail(): void
    {
        $exist = $this->repo->existsWithEmail(new EmailAddress('test@example.com'));
        $this->assertFalse($exist);
    }

    /**
     * @depends testGetByEmail
     * @param Entity $user
     * @return UniqueId
     */
    public function testGetById(Entity $user): UniqueId
    {
        try {
            $entity = $this->repo->getById($user->id());
            $this->assertInstanceOf(
                Entity::class,
                $entity
            );
            /* @noinspection PhpUndefinedMethodInspection */
            return $entity->getUuid();
        } catch (NotFoundException $e) {
            $this->assertTrue(false, $e->getMessage());
        }
        return null;
    }

    /**
     * @depends testGetById
     * @param UniqueId $uuid
     */
    public function testGetByUuid(UniqueId $uuid): void
    {
        try {
            $user = $this->repo->getByUuid($uuid);
            $this->assertInstanceOf(
                Entity::class,
                $user
            );
        } catch (NotFoundException $e) {
            $this->assertTrue(false, $e->getMessage());
        }
    }

    /** @noinspection PhpUnhandledExceptionInspection */
    public function testGetByIdNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $this->repo->getById(10000);
    }

    /**
     * @depends testGetByEmail
     * @param Entity<User> $user
     */
    public function testGetByAccessToken(Entity $user): void
    {
        $accessTokenRepo = new AccessTokenDatabaseRepository(self::$db);
        $accessTokens = $accessTokenRepo->getTokensForUser($user);
        if (count($accessTokens) > 0) {
            try {
                $user = $this->repo->getByAccessToken(
                    $accessTokens[0]->getIdentifier()
                );
                $this->assertInstanceOf(
                    Entity::class,
                    $user
                );
            } catch (NotFoundException $e) {
                $this->assertTrue(false, $e->getMessage());
            }
        }
    }
}
