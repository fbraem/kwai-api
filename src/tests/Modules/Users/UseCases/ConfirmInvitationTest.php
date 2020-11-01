<?php
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
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\UserAccount;
use Kwai\Modules\Users\Domain\ValueObjects\Username;
use Kwai\Modules\Users\Infrastructure\Repositories\UserDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\UseCases\ConfirmInvitation;
use Kwai\Modules\Users\UseCases\ConfirmInvitationCommand;
use Kwai\Modules\Users\Domain\UserInvitation;
use Tests\Context;

$context = Context::createContext();

beforeAll(function () use ($context) {
    $context->creator = new Entity(1, new User((object)[
        'uuid' => new UniqueId(),
        'emailAddress' => new EmailAddress('webmaster@kwai.com'),
        'traceableTime' => new TraceableTime(),
        'remark' => 'This is test admin user',
        'username' => new Username('Webmaster')
    ]));

    $context->userRepo = new class($context->db) extends UserDatabaseRepository {
        public function create(UserAccount $account): Entity
        {
            return new Entity(1, $account);
        }
    };
});

it('can confirm an invitation', function () use ($context) {
    $userInvitationRepo = new class($context->db, $context->creator) extends UserInvitationDatabaseRepository {
        private Entity $creator;

        public function __construct(Connection $db, Entity $creator)
        {
            parent::__construct($db);
            $this->creator = $creator;
        }

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

    $command = new ConfirmInvitationCommand();
    $command->firstName = 'Jigoro';
    $command->lastName = 'Kano';
    $command->uuid = '';
    $command->remark = 'This is a user confirmed using a unit test';
    $command->password = 'Hajime';
    $command->email = 'jigoro.kano@kwai.com';

    try {
        $user = (new ConfirmInvitation(
            $userInvitationRepo,
            $context->userRepo
        ))($command);
        expect($user->domain())
            ->toBeInstanceOf(UserAccount::class)
        ;
    } catch (NotFoundException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (UnprocessableException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can handle an expired invitation', function () use ($context) {
    $userInvitationRepo = new class($context->db, $context->creator) extends UserInvitationDatabaseRepository {
        private Entity $creator;

        public function __construct(Connection $db, Entity $creator)
        {
            parent::__construct($db);
            $this->creator = $creator;
        }

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

    $command = new ConfirmInvitationCommand();
    $command->firstName = 'Jigoro';
    $command->lastName = 'Kano';
    $command->uuid = '';
    $command->remark = 'This is a user confirmed using a unit test';
    $command->password = 'Hajime';
    $command->email = 'jigoro.kano@kwai.com';

    try {
        /** @noinspection PhpUnhandledExceptionInspection */
        (new ConfirmInvitation(
            $userInvitationRepo,
            $context->userRepo
        ))($command);
    } catch (NotFoundException $e) {
        $this->assertTrue(false, (string) $e);
    } catch (RepositoryException $e) {
        $this->assertTrue(false, (string) $e);
    }
})
    ->throws(UnprocessableException::class)
    ->skip(!Context::hasDatabase(), 'No database available')
;
