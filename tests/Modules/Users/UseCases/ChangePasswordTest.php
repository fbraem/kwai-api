<?php
declare(strict_types=1);

use Kwai\Core\Domain\Exceptions\NotAllowedException;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\UserAccount;
use Kwai\Modules\Users\Domain\UserAccountEntity;
use Kwai\Modules\Users\Domain\UserEntity;
use Kwai\Modules\Users\Domain\ValueObjects\Password;
use Kwai\Modules\Users\Infrastructure\Repositories\UserAccountDatabaseRepository;
use Kwai\Modules\Users\UseCases\ChangePassword;
use Kwai\Modules\Users\UseCases\ChangePasswordCommand;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can change the password of the user', function () {
    $command = new ChangePasswordCommand();
    $command->password = 'Test1234';

    $user = new UserEntity(
        1,
        new User(
            new UniqueId(),
            new EmailAddress(
                'jigoro.kano@kwai.com'
            ),
            new Name('Jigoro', 'Kano')
        )
    );

    $account = new UserAccountEntity(
        1,
        new UserAccount(
            $user->domain(),
            Password::fromString('Test1234')
        )
    );

    try {
        ChangePassword::create(
            new class($this->db, $user) extends UserAccountDatabaseRepository {
                public function __construct(Connection $db, private UserEntity $user)
                {
                    parent::__construct($db);
                }

                public function get(EmailAddress $emailAddress): UserAccountEntity
                {
                    return new UserAccountEntity(
                        1,
                        new UserAccount(
                            $this->user->domain(),
                            Password::fromString('Test1234')
                        )
                    );
                }

                public function update(UserAccountEntity $account): void
                {
                }
            }
        )($command, $user);
    } catch (NotAllowedException|RepositoryException $e) {
        $this->fail((string) $e);
    }
})->expectNotToPerformAssertions();

it('cannot change the password of a revoked user', function () {
    $command = new ChangePasswordCommand();
    $command->password = 'Test1234';

    $user = new UserEntity(
        1,
        new User(
            new UniqueId(),
            new EmailAddress(
                'jigoro.kano@kwai.com'
            ),
            new Name('Jigoro', 'Kano')
        )
    );

    try {
        ChangePassword::create(
            new class($this->db, $user) extends UserAccountDatabaseRepository {
                public function __construct(Connection $db, private UserEntity $user)
                {
                    parent::__construct($db);
                }

                public function get(EmailAddress $emailAddress): UserAccountEntity
                {
                    return new UserAccountEntity(
                        1,
                        new UserAccount(
                            $this->user->domain(),
                            Password::fromString('Test1234'),
                            revoked: true
                        )
                    );
                }

                public function update(UserAccountEntity $account): void
                {
                }
            }
        )($command, $user);
    } catch (RepositoryException $e) {
        $this->fail((string) $e);
    }
})->throws(NotAllowedException::class);
