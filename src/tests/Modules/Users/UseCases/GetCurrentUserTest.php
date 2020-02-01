<?php
/**
 * Testcase for TokenIdentifier
 */
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\EmailAddress;

use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\UseCases\GetCurrentUserCommand;
use Kwai\Modules\Users\UseCases\GetCurrentUser;

require_once('Database.php');

/**
 * @group DB
 */
final class GetCurrentUserTest extends TestCase
{
    private $repo;

    private $user;

    public function setup(): void
    {
        $this->repo = new UserDatabaseRepository(\Database::getDatabase());
        $this->user = $this->repo->getByEmail(new EmailAddress('test@kwai.com'));
    }

    public function testGetCurrentUser(): void
    {
        $command = new GetCurrentUserCommand([
            'uuid' => strval($this->user->getUuid())
        ]);

        $user = (new GetCurrentUser($this->repo))($command);

        $this->assertInstanceOf(
            Entity::class,
            $user
        );
    }
}
