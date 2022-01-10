<?php
/**
 * @package    Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\UseCases;

use DateTime;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\UnprocessableException;
use Kwai\Core\Domain\ValueObjects\LocalTimestamp;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Core\Infrastructure\Template\MailTemplate;
use Kwai\Modules\Mails\Domain\Mail;
use Kwai\Modules\Mails\Domain\Recipient;
use Kwai\Modules\Mails\Domain\ValueObjects\Address;
use Kwai\Modules\Mails\Domain\ValueObjects\MailContent;
use Kwai\Modules\Mails\Domain\ValueObjects\RecipientType;
use Kwai\Modules\Mails\Repositories\MailRepository;
use Kwai\Modules\Users\Domain\UserInvitation;
use Kwai\Modules\Users\Repositories\UserAccountRepository;
use Kwai\Modules\Users\Repositories\UserInvitationRepository;

/**
 * Usecase: Invite user.
 * - Step 1 - Check if the email address isn't used yet by another user
 * - Step 2 - Check if there is a previous non-expired invitation
 * - Step 3 - Create the invitation
 * - Step 4 - Create the mail
 *
 * The email is not send! It's stored in the database.
 *
 * @todo: Move EmailRepository and Email domain to this module.
 */
final class InviteUser
{
    /**
     * Constructor.
     *
     * @param UserInvitationRepository $userInvitationRepo
     * @param UserAccountRepository    $userAccountRepo
     * @param MailRepository           $mailRepo
     * @param MailTemplate             $template A template to generate the mail body
     * @param Creator                  $creator  The user that will execute this use case
     */
    public function __construct(
        private UserInvitationRepository $userInvitationRepo,
        private UserAccountRepository $userAccountRepo,
        private MailRepository $mailRepo,
        private MailTemplate $template,
        private Creator $creator
    ) {
    }

    /**
     * Factory method
     *
     * @param UserInvitationRepository $userInvitationRepo
     * @param UserAccountRepository    $userAccountRepo
     * @param MailRepository           $mailRepo
     * @param MailTemplate             $template
     * @param Creator                  $creator
     * @return static
     */
    public static function create(
        UserInvitationRepository $userInvitationRepo,
        UserAccountRepository $userAccountRepo,
        MailRepository $mailRepo,
        MailTemplate $template,
        Creator $creator
    ): self {
        return new self(
            $userInvitationRepo,
            $userAccountRepo,
            $mailRepo,
            $template,
            $creator
        );
    }

    /**
     * Create an invitation and create a mail.
     *
     * @param InviteUserCommand $command
     * @return Entity<UserInvitation> A user invitation
     * @throws UnprocessableException
     * @throws RepositoryException
     */
    public function __invoke(InviteUserCommand $command): Entity
    {
        $email = new EmailAddress($command->email);
        if ($this->userAccountRepo->existsWithEmail($email)) {
            throw new UnprocessableException(
                strval($email) . ' is already in use.'
            );
        }

        $query = $this->userInvitationRepo->createQuery();
        $query->filterByEmail($email);
        $invitations = $this->userInvitationRepo->getAll($query);
        if ($invitations->contains(fn ($invitation) => $invitation->isValid())) {
            throw new UnprocessableException(
                'An invitation is still pending for ' . $email
            );
        }

        $invitation = $this->userInvitationRepo->create(
            new UserInvitation(
                emailAddress: new EmailAddress($command->email),
                expiration: new LocalTimestamp(
                    Timestamp::createFromDateTime(
                        new DateTime("now +{$command->expiration} days")
                    ),
                    'UTC'
                ),
                name: $command->name,
                creator: $this->creator,
                remark: $command->remark,
            )
        );

        /** @noinspection PhpUndefinedMethodInspection */
        $templateVars = [
            'uuid' => $invitation->getUniqueId(),
            'name' => $command->name,
            'expires' => $invitation->getExpiration()->diffInDays()
        ];

        /** @noinspection PhpUndefinedMethodInspection */
        $mail = new Mail(
            uuid: $invitation->getUniqueId(),
            sender: new Address(
                new EmailAddress($command->sender_mail),
                $command->sender_name
            ),
            content: new MailContent(
                $this->template->getSubject(),
                $this->template->renderHtml($templateVars),
                $this->template->renderPlainText($templateVars)
            ),
            creator: $this->creator,
            tag: 'user.invitation',
            recipients: collect([
                new Recipient(
                    type: RecipientType::TO(),
                    address: new Address(
                        new EmailAddress($command->email),
                        $command->name
                    )
                )
            ])
        );
        $this->mailRepo->create($mail);

        return $invitation;
    }
}
