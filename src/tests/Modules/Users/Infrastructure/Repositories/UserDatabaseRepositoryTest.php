<?php
/**
 * Testcase for Date
 */
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\Entity;

use Kwai\Modules\Users\Infrastructure\UserTable;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;

require_once('Database.php');
// echo var_dump(Database::getDatabase()), PHP_EOL;

/**
 * @group DB
 */
final class UserDatabaseRepositoryTest extends TestCase
{
    public function testGetById(): void
    {
        $repo = new UserDatabaseRepository(\Database::getDatabase());
        $user = $repo->getById(1);
        $this->assertInstanceOf(
            Entity::class,
            $user
        );
    }

    public function testGetByIdNotFound(): void
    {
        $this->expectException(NotFoundException::class);
        $repo = new UserDatabaseRepository(Database::getDatabase());
        $user = $repo->getById(10000);
    }

    public function testGetByAccessToken(): void
    {
        $repo = new UserDatabaseRepository(Database::getDatabase());
        $user = $repo->getByAccessToken(
            new TokenIdentifier('dc23ea481a27e4ec1bc6ea20923bf4eb7b10e63f7a5df74e1be486a74a46c8ed7944c7287d234895')
        );
        $this->assertInstanceOf(
            Entity::class,
            $user
        );
    }
}
