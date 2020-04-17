<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Tests\Modules\Users\UseCases;

use DateTime;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\Exceptions\UnprocessableException;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\UserAccount;
use Kwai\Modules\Users\Domain\ValueObjects\Username;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\Repositories\UserRepository;
use Kwai\Modules\Users\UseCases\ConfirmInvitation;
use Kwai\Modules\Users\UseCases\ConfirmInvitationCommand;
use Kwai\Modules\Users\Domain\UserInvitation;
use Tests\DatabaseTestCase;

class ConfirmInvitationTest extends DatabaseTestCase
{
    private UserRepository $userRepo;

    private Entity $creator;

    public function setUp(): void
    {
        parent::setUp();

        $this->creator = new Entity(1, new User((object)[
            'uuid' => new UniqueId(),
            'emailAddress' => new EmailAddress('webmaster@kwai.com'),
            'traceableTime' => new TraceableTime(),
            'remark' => 'This is test admin user',
            'username' => new Username('Webmaster')
        ]));

        $this->userRepo = new class(self::$db) extends UserDatabaseRepository {
            public function create(UserAccount $account): Entity
            {
                return new Entity(1, $account);
            }
        };
    }

    public function testConfirm()
    {
        $userInvitationRepo = new class(self::$db) extends UserInvitationDatabaseRepository {
            public Entity $creator;

            public function getByUniqueId(UniqueId $uuid): Entity
            {
                return new Entity(1, new UserInvitation((object)[
                  'uuid' => new UniqueId(),
                  'emailAddress' => new EmailAddress('jigoro.kono@kwai.com'),
                  'name' => 'Jigoro Kono',
                  'creator' => $this->creator,
                  'remark' => 'This is a test invitation',
                  'confirmation' => null,
                  'traceableTime' => new TraceableTime(),
                  'expiration' => Timestamp::createFromDateTime(new DateTime('now +15 days'))
                ]));
            }
            public function update(Entity $invitation): void
            {
                // Do nothing ...
            }
        };
        $userInvitationRepo->creator = $this->creator;

        $command = new ConfirmInvitationCommand();
        $command->firstName = 'Jigoro';
        $command->lastName = 'Kano';
        $command->uuid = '';
        $command->remark = 'This is a user confirmed using a unit test';
        $command->password = 'Hajime';

        try {
            $user = (new ConfirmInvitation(
                $userInvitationRepo,
                $this->userRepo
            ))($command);
            self::assertInstanceOf(UserAccount::class, $user->domain());
        } catch (NotFoundException $e) {
            self::assertTrue(false, strval($e));
        } catch (UnprocessableException $e) {
            self::assertTrue(false, strval($e));
        } catch (RepositoryException $e) {
            self::assertTrue(false, strval($e));
        }
    }

    public function testExpired()
    {
        $userInvitationRepo = new class(self::$db) extends UserInvitationDatabaseRepository {
            public Entity $creator;

            public function getByUniqueId(UniqueId $uuid): Entity
            {
                return new Entity(1, new UserInvitation((object)[
                    'uuid' => new UniqueId(),
                    'emailAddress' => new EmailAddress('jigoro.kono@kwai.com'),
                    'name' => 'Jigoro Kono',
                    'creator' => $this->creator,
                    'remark' => 'This is a test invitation',
                    'confirmation' => null,
                    'traceableTime' => new TraceableTime(),
                    'expiration' => Timestamp::createFromDateTime(new DateTime('now -1 days'))
                ]));
            }
            public function update(Entity $invitation): void
            {
                // Do nothing ...
            }
        };
        $userInvitationRepo->creator = $this->creator;

        $command = new ConfirmInvitationCommand();
        $command->firstName = 'Jigoro';
        $command->lastName = 'Kano';
        $command->uuid = '';
        $command->remark = 'This is a user confirmed using a unit test';
        $command->password = 'Hajime';

        $this->expectException(UnprocessableException::class);
        /** @noinspection PhpUnhandledExceptionInspection */
        (new ConfirmInvitation(
            $userInvitationRepo,
            $this->userRepo
        ))($command);
    }
}
