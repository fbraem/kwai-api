<?php
/**
 * Testcase for TokenIdentifier
 */
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\EmailAddress;

use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\AbilityDatabaseRepository;
use Kwai\Modules\Users\UseCases\GetCurrentUserCommand;
use Kwai\Modules\Users\UseCases\GetCurrentUser;

require_once('Database.php');

/**
 * @group DB
 */
final class GetCurrentUserTest extends TestCase
{
    private $userRepo;

    private $abilityRepo;

    private $user;

    public function setup(): void
    {
        $this->userRepo = new UserDatabaseRepository(\Database::getDatabase());
        $this->abilityRepo = new AbilityDatabaseRepository(\Database::getDatabase());
        $this->user = $this->userRepo->getByEmail(new EmailAddress('test@kwai.com'));
    }

    public function testGetCurrentUser(): void
    {
        $command = new GetCurrentUserCommand([
            'uuid' => strval($this->user->getUuid())
        ]);

        $user = (new GetCurrentUser(
            $this->userRepo,
            $this->abilityRepo
        ))($command);

        $this->assertInstanceOf(
            Entity::class,
            $user
        );
    }
}
