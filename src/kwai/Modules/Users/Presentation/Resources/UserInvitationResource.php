<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Presentation\Resources;

use Kwai\Core\Domain\Entity;
use Kwai\JSONAPI;
use Kwai\Modules\Users\Domain\UserInvitation;

/**
 * Class UserInvitationResource
 */
#[JSONAPI\Resource(type: 'user_invitations', id: 'getId')]
class UserInvitationResource
{
    /**
     * @param Entity<UserInvitation> $userInvitation
     */
    public function __construct(
        private Entity $userInvitation
    ) {
    }

    public function getId(): string
    {
        return (string) $this->userInvitation->id();
    }

    #[JSONAPI\Attribute(name: 'uuid')]
    public function getUuid(): string
    {
        return (string) $this->userInvitation->getUuid();
    }

    #[JSONAPI\Attribute(name: 'email')]
    public function getEmail(): string
    {
        return (string) $this->userInvitation->getEmailAddress();
    }

    #[JSONAPI\Attribute(name: 'username')]
    public function getUsername(): string
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
        return (string) $this->userInvitation->getExpiration();
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