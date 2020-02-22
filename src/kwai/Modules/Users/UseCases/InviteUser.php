<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Domain\Timestamp;
use Kwai\Core\Domain\EmailAddress;
use Kwai\Core\Domain\TraceableTime;

use Kwai\Modules\Users\Repositories\UserInvitationRepository;

use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\UserInvitation;

/**
 * Usecase: Invite user
 */
final class InviteUser
{
    /**
     * @var Entity<User>
     */
    private $user;

    /**
     * @var UserInvitationRepository
     */
    private $userInvitationRepo;

    /**
     * Constructor.
     * @param UserInvitationRepository $userInvitationRepo A user invitation repo
     * @param Entity<User> $user The user that will execute this use case
     */
    public function __construct(
        UserInvitationRepository $userInvitationRepo,
        Entity $user
    ) {
        $this->userInvitationRepo = $userInvitationRepo;
        $this->user = $user;
    }

    /**
     * Create an invitation and create a mail.
     * @param  InviteUserCommand $command
     * @return Entity<UserInvitation> A user invitation
     */
    public function __invoke(InviteUserCommand $command): Entity
    {
        return $this->userInvitationRepo->create(new UserInvitation(
            (object) [
                'uuid' => new UniqueId(),
                'emailAddress' => new EmailAddress($command->email),
                'traceableTime' => new TraceableTime(),
                'expiration' => Timestamp::createFromDateTime(
                    new \DateTime("now +{$command->expiration} days")
                ),
                'remark' => $command->remark,
                'name' => $command->name,
                'creator' => $this->user
            ]
        ));
    }
}
