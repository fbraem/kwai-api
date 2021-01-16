<?php
declare(strict_types=1);

namespace Tests\Modules\Users\UseCases;

use DateTime;
use Exception;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\Exceptions\UnprocessableException;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\UserAccount;
use Kwai\Modules\Users\Infrastructure\Repositories\UserAccountDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\UseCases\ConfirmInvitation;
use Kwai\Modules\Users\UseCases\ConfirmInvitationCommand;
use Kwai\Modules\Users\Domain\UserInvitation;
use Tests\Context;

$context = Context::createContext();

beforeAll(function () use ($context) {
    $context->creator = new Creator(
        id: 1,
        name: new Name('Jigoro', 'Kano')
    );

    $context->userAccountRepo = new class($context->db) extends UserAccountDatabaseRepository {
        public function create(UserAccount $account): Entity
        {
            return new Entity(1, $account);
        }
    };
});

it('can confirm an invitation', function () use ($context) {
    $userInvitationRepo = new class($context->db, $context->creator) extends UserInvitationDatabaseRepository {
        public function __construct(
            Connection $db,
            private Creator $creator
        ) {
            parent::__construct($db);
        }

        public function getByUniqueId(UniqueId $uuid): Entity
        {
            return new Entity(1, new UserInvitation(
                uuid: new UniqueId(),
                emailAddress: new EmailAddress('gella.vandecavye@kwai.com'),
                name: 'Gella Vandecaveye',
                creator: $this->creator,
                remark: 'This is a test invitation',
                expiration: Timestamp::createFromDateTime(new DateTime('now +15 days'))
            ));
        }
        public function update(Entity $invitation): void
        {
            // Do nothing ...
        }
    };

    $command = new ConfirmInvitationCommand();
    $command->firstName = 'Gella';
    $command->lastName = 'Vandecavye';
    $command->uuid = '';
    $command->remark = 'This is a user confirmed using a unit test';
    $command->password = 'Hajime';
    $command->email = 'gella.vandecavye@kwai.com';

    try {
        $user = (new ConfirmInvitation(
            $userInvitationRepo,
            $context->userAccountRepo
        ))($command);
        expect($user->domain())
            ->toBeInstanceOf(UserAccount::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(!Context::hasDatabase(), 'No database available')
;

it('can handle an expired invitation', function () use ($context) {
    $userInvitationRepo = new class($context->db, $context->creator) extends UserInvitationDatabaseRepository {
        public function __construct(
            Connection $db,
            private Creator $creator
        ) {
            parent::__construct($db);
            $this->creator = $creator;
        }

        public function getByUniqueId(UniqueId $uuid): Entity
        {
            return new Entity(1, new UserInvitation(
                uuid: new UniqueId(),
                emailAddress: new EmailAddress('gella.vandecavye@kwai.com'),
                name: 'Gella Vandecaveye',
                creator: $this->creator,
                remark: 'This is a test invitation',
                expiration: Timestamp::createFromDateTime(new DateTime('now -1 days'))
            ));
        }
        public function update(Entity $invitation): void
        {
            // Do nothing ...
        }
    };

    $command = new ConfirmInvitationCommand();
    $command->firstName = 'Gella';
    $command->lastName = 'Vandecaveye';
    $command->uuid = '';
    $command->remark = 'This is a user confirmed using a unit test';
    $command->password = 'Hajime';
    $command->email = 'gella.vandecaveye@kwai.com';

    try {
        /** @noinspection PhpUnhandledExceptionInspection */
        (new ConfirmInvitation(
            $userInvitationRepo,
            $context->userAccountRepo
        ))($command);
    } catch (NotFoundException $e) {
        $this->fail((string) $e);
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
})
    ->throws(UnprocessableException::class)
    ->skip(!Context::hasDatabase(), 'No database available')
;
