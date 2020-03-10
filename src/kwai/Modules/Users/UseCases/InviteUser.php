<?php
/**
 * @package    Kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\UseCases;

use DateTime;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Domain\Timestamp;
use Kwai\Core\Domain\EmailAddress;
use Kwai\Core\Domain\TraceableTime;
use Kwai\Core\Domain\Exceptions\AlreadyExistException;

use Kwai\Core\Infrastructure\Template\MailTemplate;
use Kwai\Modules\Mails\Domain\Mail;
use Kwai\Modules\Mails\Domain\Recipient;
use Kwai\Modules\Mails\Domain\ValueObjects\Address;
use Kwai\Modules\Mails\Domain\ValueObjects\MailContent;
use Kwai\Modules\Mails\Domain\ValueObjects\RecipientType;
use Kwai\Modules\Mails\Repositories\MailRepository;
use Kwai\Modules\Mails\Repositories\RecipientRepository;
use Kwai\Modules\Users\Repositories\UserInvitationRepository;
use Kwai\Modules\Users\Repositories\UserRepository;

use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\UserInvitation;

/**
 * Usecase: Invite user.
 * - Step 1 - Check if the email address isn't used yet by another user
 * - Step 2 - Check if there is a previous non-expired invitation
 * - Step 3 - Create the invitation
 * - Step 4 - Create the mail
 *
 * The email is not send! It's stored in the database.
 */
final class InviteUser
{
    /**
     * The active user.
     * @var Entity<User>
     */
    private Entity $user;

    /**
     * Repository for user invitation.
     */
    private UserInvitationRepository $userInvitationRepo;

    /**
     *  Repository for user.
     */
    private UserRepository $userRepo;

    /**
     * Repository for mail
     */
    private MailRepository $mailRepo;

    /**
     * Repository for recipients
     */
    private RecipientRepository $recipientRepo;

    /**
     * Template for generating the invitation mail.
     */
    private MailTemplate $template;

    /**
     * Constructor.
     *
     * @param UserInvitationRepository $userInvitationRepo A user invitation repo
     * @param UserRepository $userRepo A user repo
     * @param MailRepository $mailRepo A mail repo
     * @param RecipientRepository $recipientRepo A recipient repo
     * @param MailTemplate $template A template to generate the mail body
     * @param Entity<User> $user The user that will execute this use case
     */
    public function __construct(
        UserInvitationRepository $userInvitationRepo,
        UserRepository $userRepo,
        MailRepository $mailRepo,
        RecipientRepository $recipientRepo,
        MailTemplate $template,
        Entity $user
    ) {
        $this->userInvitationRepo = $userInvitationRepo;
        $this->userRepo = $userRepo;
        $this->mailRepo = $mailRepo;
        $this->recipientRepo = $recipientRepo;
        $this->template = $template;
        $this->user = $user;
    }

    /**
     * Create an invitation and create a mail.
     *
     * @param  InviteUserCommand $command
     * @return Entity<UserInvitation> A user invitation
     * @throws AlreadyExistException
     */
    public function __invoke(InviteUserCommand $command): Entity
    {
        $email = new EmailAddress($command->email);
        if ($this->userRepo->existsWithEmail($email)) {
            throw new AlreadyExistException(
                'User',
                strval($email) . ' is already in use.'
            );
        }

        $invitations = $this->userInvitationRepo->getByEmail($email);
        foreach ($invitations as $invitation) {
            if ($invitation->isValid()) {
                throw new AlreadyExistException(
                    'UserInvitation',
                    'An invitation is still pending for ' . $email
                );
            }
        }

        $invitation = $this->userInvitationRepo->create(
            new UserInvitation(
                (object) [
                'uuid' => new UniqueId(),
                'emailAddress' => new EmailAddress($command->email),
                'traceableTime' => new TraceableTime(),
                'expiration' => Timestamp::createFromDateTime(
                    new DateTime("now +{$command->expiration} days")
                ),
                'remark' => $command->remark,
                'name' => $command->name,
                'creator' => $this->user,
                'revoked' => false
                ]
            )
        );

        /** @noinspection PhpUndefinedMethodInspection */
        $templateVars = [
            'uuid' => $invitation->getUniqueId(),
            'name' => $command->name
        ];

        /** @noinspection PhpUndefinedMethodInspection */
        $mail = new Mail(
            (object) [
                'tag' => 'user.invitation',
                'uuid' =>  $invitation->getUniqueId(),
                'sender' => new Address(
                    new EmailAddress($command->sender_mail),
                    $command->sender_name
                ),
                'content' => new MailContent(
                    $this->template->getSubject(),
                    $this->template->renderHtml($templateVars),
                    $this->template->renderPlainText($templateVars)
                ),
                'creator' => $this->user
            ]
        );
        $mailEntity = $this->mailRepo->create(
            $mail
        );

        $recipients = $this->recipientRepo->create(
            $mailEntity,
            [
            new Recipient(
                (object) [
                    'type' => RecipientType::TO(),
                    'address' => new Address(
                        new EmailAddress($command->email),
                        $command->name
                    )
                ]
            )]
        );
        foreach ($recipients as $recipient) {
            /** @noinspection PhpUndefinedMethodInspection */
            $mailEntity->addRecipient($recipient);
        }

        return $invitation;
    }
}
