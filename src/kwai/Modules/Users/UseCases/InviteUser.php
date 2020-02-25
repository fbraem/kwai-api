<?php
/**
 * @package Kwai
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

use Kwai\Modules\Users\Repositories\UserInvitationRepository;
use Kwai\Modules\Users\Repositories\UserRepository;
use Kwai\Modules\Mails\Repositories\MailRepository;

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
     * @var Entity<User>
     */
    private Entity $user;

    private UserInvitationRepository $userInvitationRepo;

    private UserRepository $userRepo;

    private MailRepository $mailRepo;

    /**
     * Constructor.
     * @param UserInvitationRepository $userInvitationRepo A user invitation repo
     * @param UserRepository $userRepo A user repo
     * @param MailRepository $mailRepo An email repo
     * @param Entity<User> $user The user that will execute this use case
     */
    public function __construct(
        UserInvitationRepository $userInvitationRepo,
        UserRepository $userRepo,
        MailRepository $mailRepo,
        Entity $user
    ) {
        $this->userInvitationRepo = $userInvitationRepo;
        $this->userRepo = $userRepo;
        $this->mailRepo = $mailRepo;
        $this->user = $user;
    }

    /**
     * Create an invitation and create a mail.
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
        foreach($invitations as $invitation) {
            if ($invitation->isValid()) {
                throw new AlreadyExistException(
                    'UserInvitation',
                    'An invitation is still pending for ' . $email
                );
            }
        }

        $invitation = $this->userInvitationRepo->create(new UserInvitation(
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
        ));

        return $invitation;
    }
}
