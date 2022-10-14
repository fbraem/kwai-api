<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\UseCases;

use DateTime;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\LocalTimestamp;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Mailer\MailerService;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Core\Infrastructure\Template\MailTemplate;
use Kwai\Modules\Users\Domain\Exceptions\UserAccountNotFoundException;
use Kwai\Modules\Users\Domain\UserEntity;
use Kwai\Modules\Users\Domain\UserRecovery;
use Kwai\Modules\Users\Domain\UserRecoveryEntity;
use Kwai\Modules\Users\Mailers\UserRecoveryMailer;
use Kwai\Modules\Users\Repositories\UserAccountRepository;
use Kwai\Modules\Users\Repositories\UserRecoveryRepository;

/**
 * Use case for recovering a password for a user.
 * The user, if known, will receive an email with a link to reset his/her password.
 */
final class RecoverUser
{
    public function __construct(
        private readonly UserRecoveryRepository $userRecoverRepo,
        private readonly UserAccountRepository $userAccountRepo,
        private readonly MailerService $mailerService,
        private readonly MailTemplate $template
    ) {
    }

    /**
     * Factory method.
     */
    public static function create(
        UserRecoveryRepository $userRecoverRepo,
        UserAccountRepository $userAccountRepo,
        MailerService $mailerService,
        MailTemplate $template
    ): RecoverUser
    {
        return new self(
            $userRecoverRepo,
            $userAccountRepo,
            $mailerService,
            $template
        );
    }

    /**
     * @throws RepositoryException
     * @throws UserAccountNotFoundException
     */
    public function __invoke(RecoverUserCommand $command): ?UserRecoveryEntity
    {
        $receiverEmail = new EmailAddress($command->email);
        $account = $this->userAccountRepo->get($receiverEmail);
        // Don't recover a revoked account.
        if ($account->isRevoked()) {
            return null;
        }

        $recovery = $this->userRecoverRepo->create(
            new UserRecovery(
                uuid: new UniqueId(),
                expiration: new LocalTimestamp(
                    Timestamp::createFromDateTime(
                        new DateTime("now +{$command->expiration} hours")
                    ),
                    'UTC'
                ),
                user: new UserEntity(
                    $account->id(),
                    $account->getUser()
                )
            )
        );

        (new UserRecoveryMailer(
            $this->mailerService,
            $this->template,
            $recovery
        ))->send();

        return $recovery;
    }
}