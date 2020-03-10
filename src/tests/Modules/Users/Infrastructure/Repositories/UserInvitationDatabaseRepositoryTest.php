<?php
/**
 * Testcase for Date
 */
declare(strict_types=1);

namespace Tests\Modules\Users\Infrastructure\Repositories;

use DateTime;
use Exception;
use Kwai\Core\Domain\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\Timestamp;
use Kwai\Core\Domain\TraceableTime;
use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Kwai\Modules\Users\Domain\UserInvitation;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\Repositories\UserInvitationRepository;
use Kwai\Modules\Users\Repositories\UserRepository;
use Tests\DatabaseTestCase;

/**
 * @group DB
 */
final class UserInvitationDatabaseRepositoryTest extends DatabaseTestCase
{
    private UserRepository $userRepo;

    private UserInvitationRepository $repo;

    public function setup(): void
    {
        $this->repo = new UserInvitationDatabaseRepository(self::$db);
        $this->userRepo = new UserDatabaseRepository(self::$db);
    }

    /**
     * @return Entity<UserInvitation>
     */
    public function testCreate(): Entity
    {
        try {
            $user = $this->userRepo->getByEmail(new EmailAddress('test@kwai.com'));
            $invitation = new UserInvitation((object) [
                'uuid' => new UniqueId(),
                'emailAddress' => new EmailAddress('invite@kwai.com'),
                'traceableTime' => new TraceableTime(),
                'expiration' => Timestamp::createFromDateTime(new DateTime("now +14 days")),
                'name' => 'Jigoro Kano',
                'creator' => $user
            ]);
            $invitation = $this->repo->create($invitation);
            $this->assertInstanceOf(Entity::class, $invitation);
            return $invitation;
        } catch (DatabaseException $e) {
            $this->assertTrue(false, strval($e));
        } catch (NotFoundException $e) {
            $this->assertTrue(false, $e->getMessage());
        } catch (Exception $e) {
            $this->assertTrue(false, $e->getMessage());
        }
        return null;
    }

    /**
     * @depends testCreate
     */
    public function testGetByEmail(): void
    {
        try {
            $invitations = $this->repo->getByEmail(new EmailAddress('invite@kwai.com'));
            $this->assertContainsOnlyInstancesOf(
                Entity::class,
                $invitations
            );
        } catch (DatabaseException $e) {
            $this->assertTrue(false, 'DatabaseException occurred: ' . $e->getMessage());
        }
    }
}
