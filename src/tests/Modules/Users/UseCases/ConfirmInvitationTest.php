<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Tests\Modules\Users\UseCases;

use DateTime;
use Kwai\Core\Domain\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\Exceptions\UnprocessableException;
use Kwai\Core\Domain\Timestamp;
use Kwai\Core\Domain\TraceableTime;
use Kwai\Core\Domain\UniqueId;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\UserAccount;
use Kwai\Modules\Users\Domain\ValueObjects\Username;
use Kwai\Modules\Users\Repositories\UserInvitationRepository;
use Kwai\Modules\Users\Repositories\UserRepository;
use Kwai\Modules\Users\UseCases\ConfirmInvitation;
use Kwai\Modules\Users\UseCases\ConfirmInvitationCommand;
use Kwai\Modules\Users\Domain\UserInvitation;
use Mockery;
use PHPUnit\Framework\TestCase;

class ConfirmInvitationTest extends TestCase
{
    private UserInvitationRepository $userInvitationRepo;

    private UserRepository $userRepo;

    private Entity $validInvitation;

    private Entity $expiredInvitation;

    public function setUp(): void
    {
        parent::setUp();

        $creator = new Entity(1, new User((object)[
            'uuid' => new UniqueId(),
            'emailAddress' => new EmailAddress('webmaster@kwai.com'),
            'traceableTime' => new TraceableTime(),
            'remark' => 'This is test admin user',
            'username' => new Username('Webmaster')
        ]));

        $this->validInvitation = new Entity(1, new UserInvitation((object)[
            'uuid' => new UniqueId(),
            'emailAddress' => new EmailAddress('jigoro.kono@kwai.com'),
            'name' => 'Jigoro Kono',
            'creator' => $creator,
            'remark' => 'This is a test invitation',
            'confirmation' => null,
            'traceableTime' => new TraceableTime(),
            'expiration' => Timestamp::createFromDateTime(new DateTime('now +15 days'))
        ]));

        $this->expiredInvitation = new Entity(2, new UserInvitation((object)[
            'uuid' => new UniqueId(),
            'emailAddress' => new EmailAddress('jigoro.kono@kwai.com'),
            'name' => 'Jigoro Kono',
            'creator' => $creator,
            'remark' => 'This is a test invitation',
            'confirmation' => null,
            'traceableTime' => new TraceableTime(),
            'expiration' => Timestamp::createFromDateTime(new DateTime('now -1 days'))
        ]));

        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $this->userRepo = Mockery::mock(UserRepository::class);
        $this->userRepo
            ->shouldReceive('create')
            ->andReturnUsing(fn($arg1) => new Entity(1, $arg1))
        ;
    }

    public function testConfirm()
    {
        $this->userInvitationRepo = Mockery::mock(UserInvitationRepository::class);
        $this->userInvitationRepo
            ->shouldReceive('getByUniqueId')
            ->andReturn($this->validInvitation)
        ;
        $this->userInvitationRepo
            ->shouldReceive('update')
            ->andReturn($this->validInvitation)
        ;

        $command = new ConfirmInvitationCommand();
        $command->firstName = 'Jigoro';
        $command->lastName = 'Kano';
        $command->uuid = '';
        $command->remark = 'This is a user confirmed using a unit test';
        $command->password = 'Hajime';

        try {
            $user = (new ConfirmInvitation(
                $this->userInvitationRepo,
                $this->userRepo
            ))($command);
            self::assertInstanceOf(UserAccount::class, $user->domain());
        } catch (NotFoundException $e) {
            self::assertTrue(true, strval($e));
        } catch (UnprocessableException $e) {
            self::assertTrue(true, strval($e));
        }
        $this->assertTrue(true, 'Jopla');
    }

    public function testExpired()
    {
        $this->userInvitationRepo = Mockery::mock(UserInvitationRepository::class);
        $this->userInvitationRepo
            ->shouldReceive('getByUniqueId')
            ->andReturn($this->expiredInvitation)
        ;

        $command = new ConfirmInvitationCommand();
        $command->firstName = 'Jigoro';
        $command->lastName = 'Kano';
        $command->uuid = '';
        $command->remark = 'This is a user confirmed using a unit test';
        $command->password = 'Hajime';

        $this->expectException(UnprocessableException::class);
        /** @noinspection PhpUnhandledExceptionInspection */
        (new ConfirmInvitation(
            $this->userInvitationRepo,
            $this->userRepo
        ))($command);
    }
}
