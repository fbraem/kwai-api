<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Applications\Users\Resources;

use Kwai\JSONAPI;
use Kwai\Modules\Users\Domain\UserInvitationEntity;

/**
 * Class UserInvitationResource
 */
#[JSONAPI\Resource(type: 'user_invitations', id: 'getId')]
class UserInvitationResource
{
    /**
     * @param UserInvitationEntity $userInvitation
     */
    public function __construct(
        private UserInvitationEntity $userInvitation
    ) {
    }

    public function getId(): string
    {
        return (string) $this->userInvitation->getUuid();
    }

    #[JSONAPI\Attribute(name: 'email')]
    public function getEmail(): string
    {
        return (string) $this->userInvitation->getEmailAddress();
    }

    #[JSONAPI\Attribute(name: 'name')]
    public function getName(): string
    {
        return $this->userInvitation->getName();
    }

    #[JSONAPI\Attribute(name: 'remark')]
    public function getRemark(): ?string
    {
        return $this->userInvitation->getRemark();
    }

    #[JSONAPI\Attribute(name: 'expired_at')]
    public function getExpiredAt(): string
    {
        return (string) $this->userInvitation->getExpiration()->getTimestamp();
    }

    #[JSONAPI\Attribute(name: 'expired_at_timezone')]
    public function getExpiredAtTimezone(): string
    {
        return (string) $this->userInvitation->getExpiration()->getTimezone();
    }

    #[JSONAPI\Attribute(name: 'confirmed_at')]
    public function getConfirmedAt(): ?string
    {
        if ($this->userInvitation->isConfirmed()) {
            return (string) $this->userInvitation->getConfirmation();
        }
        return null;
    }

    #[JSONAPI\Attribute(name: 'created_at')]
    public function getCreationDate(): string
    {
        return (string) $this->userInvitation->getTraceableTime()->getCreatedAt();
    }

    #[JSONAPI\Attribute(name: 'updated_at')]
    public function getUpdateDate(): ?string
    {
        return $this->userInvitation->getTraceableTime()->getUpdatedAt()?->__toString();
    }
}
