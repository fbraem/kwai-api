<?php
/**
 * Testcase for Date
 */
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\Entity;

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
    public function testGetTokensForUser(): void
    {
        $repo = new UserDatabaseRepository(Database::getDatabase());
        $user = $repo->getById(1);

        $repo = new AccessTokenDatabaseRepository(Database::getDatabase());
        $accessTokens = $repo->getTokensForUser($user);
        $this->assertContainsOnlyInstancesOf(
            Entity::class,
            $accessTokens
        );
    }

    public function testGetByTokenIdentifier(): void
    {
        //TODO: Make sure the token is there ...
        $repo = new AccessTokenDatabaseRepository(Database::getDatabase());
        $accessToken = $repo->getByTokenIdentifier(
            new TokenIdentifier('dc23ea481a27e4ec1bc6ea20923bf4eb7b10e63f7a5df74e1be486a74a46c8ed7944c7287d234895')
        );
        $this->assertInstanceOf(
            Entity::class,
            $accessToken
        );
    }
}
