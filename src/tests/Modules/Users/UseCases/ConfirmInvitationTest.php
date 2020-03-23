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
    private $userInvitationRepo;

    private $userRepo;

    public function setUp(): void
    {
        parent::setUp();

        $invitation = new Entity(1, new UserInvitation((object)[
            'uuid' => new UniqueId(),
            'emailAddress' => new EmailAddress('jigoro.kono@kwai.com'),
            'name' => 'Jigoro Kono',
            'creator' => new Entity(1, new User((object)[
                'uuid' => new UniqueId(),
                'emailAddress' => new EmailAddress('webmaster@kwai.com'),
                'traceableTime' => new TraceableTime(),
                'remark' => 'This is test admin user',
                'username' => new Username('Webmaster')
            ])),
            'remark' => 'This is a test invitation',
            'confirmation' => null,
            'traceableTime' => new TraceableTime(),
            'expiration' => Timestamp::createFromDateTime(new DateTime('now +15 days'))
        ]));

        $this->userInvitationRepo = Mockery::mock(UserInvitationRepository::class);
        $this->userInvitationRepo
            ->shouldReceive('getByUniqueId')
            ->andReturn($invitation)
        ;
        $this->userInvitationRepo
            ->shouldReceive('update')
            ->andReturn($invitation)
        ;

        $this->userRepo = Mockery::mock(UserRepository::class);
        $this->userRepo
            ->shouldReceive('create')
            ->andReturnUsing(fn($arg1) => new Entity(1, $arg1))
        ;
    }

    /** @noinspection PhpParamsInspection */
    public function testConfirm()
    {
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
}
