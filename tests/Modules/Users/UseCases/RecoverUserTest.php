<?php
declare(strict_types=1);

use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Dependencies\MailerDependency;
use Kwai\Core\Infrastructure\Dependencies\TemplateDependency;
use Kwai\Core\Infrastructure\Template\MailTemplate;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\UserAccount;
use Kwai\Modules\Users\Domain\UserAccountEntity;
use Kwai\Modules\Users\Domain\UserRecoveryEntity;
use Kwai\Modules\Users\Domain\ValueObjects\Password;
use Kwai\Modules\Users\Infrastructure\Repositories\UserAccountDatabaseRepository;
use Kwai\Modules\Users\Infrastructure\Repositories\UserRecoveryDatabaseRepository;
use Kwai\Modules\Users\Repositories\UserAccountRepository;
use Kwai\Modules\Users\UseCases\RecoverUser;
use Kwai\Modules\Users\UseCases\RecoverUserCommand;
use Tests\DatabaseTrait;

uses(DatabaseTrait::class);
beforeEach(fn() => $this->withDatabase());

it('can recover a user', function() {
    $mailService = depends('kwai.mailer', MailerDependency::class);

    $command = new RecoverUserCommand();
    $command->email = 'franky.braem@gmail.com';

    $engine = depends('kwai.template', TemplateDependency::class);
    try {
        $recover = RecoverUser::create(
            new UserRecoveryDatabaseRepository($this->db),
            new UserAccountDatabaseRepository($this->db),
            $mailService,
            new MailTemplate(
                $engine->createTemplate('Users::recover_html'),
                $engine->createTemplate('Users::recover_txt')
            )
        )($command);
        expect($recover)
            ->toBeInstanceOf(UserRecoveryEntity::class)
        ;
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;

it('does not recover a revoked user', function() {
    $mailService = depends('kwai.mailer', MailerDependency::class);

    $command = new RecoverUserCommand();
    $command->email = 'franky.braem@gmail.com';

    $engine = depends('kwai.template', TemplateDependency::class);
    try {
        $recover = RecoverUser::create(
            new UserRecoveryDatabaseRepository($this->db),
            new class($this->db) extends UserAccountDatabaseRepository implements UserAccountRepository {
                public function get(EmailAddress $email): UserAccountEntity
                {
                    return new UserAccountEntity(
                        1,
                        new UserAccount(
                            user: new User(
                                new UniqueId(),
                                new EmailAddress('jigoro.kano@kwai.com'),
                                new Name('Jigoro', 'Kano')
                            ),
                            password: new Password('test1234'),
                            revoked: true
                        )
                    );
                }
            },
            $mailService,
            new MailTemplate(
                $engine->createTemplate('Users::recover_html'),
                $engine->createTemplate('Users::recover_txt')
            )
        )($command);
        expect($recover)->toBeNull();
    } catch (Exception $e) {
        $this->fail((string) $e);
    }
})
    ->skip(fn() => !$this->hasDatabase(), 'No database available')
;
