<?php
/**
 * Testcase for GetCurrentUser
 */
declare(strict_types=1);

namespace Tests\Modules\Users\UseCases;

use Kwai\Core\Domain\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Infrastructure\Repositories\AbilityDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Repositories\AbilityRepository;
use Kwai\Modules\Users\Repositories\UserRepository;
use Kwai\Modules\Users\UseCases\GetCurrentUser;
use Kwai\Modules\Users\UseCases\GetCurrentUserCommand;
use Tests\DatabaseTestCase;

/**
 * @group DB
 */
final class GetCurrentUserTest extends DatabaseTestCase
{
    private UserRepository $userRepo;

    private AbilityRepository $abilityRepo;

    /**
     * @var Entity<User>
     */
    private Entity $user;

    public function setup(): void
    {
        $this->userRepo = new UserDatabaseRepository(self::$db);
        $this->abilityRepo = new AbilityDatabaseRepository(self::$db);
        try {
            $this->user = $this->userRepo->getByEmail(new EmailAddress('test@kwai.com'));
        } catch (NotFoundException $e) {
            echo $e->getMessage(), PHP_EOL;
        }
    }

    public function testGetCurrentUser(): void
    {
        $command = new GetCurrentUserCommand();
        /** @noinspection PhpUndefinedMethodInspection */
        $command->uuid = strval($this->user->getUuid());

        try {
            $user = (new GetCurrentUser(
                $this->userRepo,
                $this->abilityRepo
            ))($command);
            $this->assertInstanceOf(
                Entity::class,
                $user
            );
        } catch (NotFoundException $e) {
            assertTrue(false, $e->getMessage());
        }
    }
}
