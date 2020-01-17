<?php
/**
 * Testcase for TokenIdentifier
 */
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Kwai\Core\Domain\Entity;

use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\UseCases\AuthenticateUser;
use Kwai\Modules\Users\UseCases\AuthenticateUserCommand;

require_once('Database.php');

/**
 * @group DB
 */
final class AuthenticateUserTest extends TestCase
{
    public function testAuthenticate(): void
    {
        $repo = new UserDatabaseRepository(Database::getDatabase());
        $command = new AuthenticateUserCommand([
            'email' => $_ENV['user'],
            'password' => $_ENV['password']
        ]);

        $user = (new AuthenticateUser($repo))($command);

        $this->assertInstanceOf(
            Entity::class,
            $user
        );
    }
}
