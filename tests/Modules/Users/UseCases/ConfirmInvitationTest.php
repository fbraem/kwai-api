<?php
declare(strict_types=1);

namespace Tests\Modules\Users\UseCases;

use DateTime;
use Exception;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\Exceptions\UnprocessableException;
use Kwai\Core\Domain\ValueObjects\LocalTimestamp;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\UserAccount;
use Kwai\Modules\Users\Domain\UserAccountEntity;
use Kwai\Modules\Users\Domain\UserInvitationEntity;
use Kwai\Modules\Users\Infrastructure\Repositories\UserAccountDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserInvitationDatabaseRepository;
use Kwai\Modules\Users\Repositories\UserAccountRepository;
use Kwai\Modules\Users\UseCases\ConfirmInvitation;
use Kwai\Modules\Users\UseCases\ConfirmInvitationCommand;
use Kwai\Modules\Users\Domain\UserInvitation;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

function createAccountRepo(Connection $db): UserAccountRepository {
    return new class($db) extends UserAccountDatabaseRepository {
        public function create(UserAccount $account): UserAccountEntity
        {
            return new UserAccountEntity(1, $account);
        }
    };
}

it('can confirm an invitation', function () {
    $userInvitationRepo = new class($this->db) extends UserInvitationDatabaseRepository {
        public function __construct(
            Connection $db
        ) {
            parent::__construct($db);
        }

        public function getByUniqueId(UniqueId $uuid): UserInvitationEntity
        {
            return new UserInvitationEntity(1, new UserInvitation(
                emailAddress: new EmailAddress('gella.vandecavye@kwai.com'),
                expiration: new LocalTimestamp(
                    Timestamp::createFromDateTime(new DateTime('now +15 days')),
                    'UTC'
                ),
                name: 'Gella Vandecaveye',
                creator: new Creator(
                    id: 1,
                    name: new Name('Jigoro', 'Kano')
                ),
                remark: 'This is a test invitation',
                uuid: new UniqueId()
            ));
        }
        public function update(UserInvitationEntity $invitation): void
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
            createAccountRepo($this->db)
        ))($command);
        expect($user->domain())
            ->toBeInstanceOf(UserAccount::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('can handle an expired invitation', function () {
    $userInvitationRepo = new class($this->db) extends UserInvitationDatabaseRepository {
        public function __construct(
            Connection $db
        ) {
            parent::__construct($db);
        }

        public function getByUniqueId(UniqueId $uuid): UserInvitationEntity
        {
            return new UserInvitationEntity(1, new UserInvitation(
                emailAddress: new EmailAddress('gella.vandecavye@kwai.com'),
                expiration: new LocalTimestamp(
                    Timestamp::createFromDateTime(new DateTime('now -1 days')),
                    'UTC'
                ),
                name: 'Gella Vandecaveye',
                creator: new Creator(
                    id: 1,
                    name: new Name('Jigoro', 'Kano')
                ),
                remark: 'This is a test invitation',
                uuid: new UniqueId()
            ));
        }
        public function update(UserInvitationEntity $invitation): void
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
            createAccountRepo($this->db)
        ))($command);
    } catch (NotFoundException $e) {
        $this->fail((string) $e);
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
})
    ->throws(UnprocessableException::class)
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
