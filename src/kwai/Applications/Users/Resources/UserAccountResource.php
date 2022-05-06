<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Applications\Users\Resources;

use Kwai\JSONAPI;
use Kwai\Modules\Users\Domain\UserAccountEntity;
use Kwai\Modules\Users\Domain\UserEntity;

/**
 * Class UserAccountResource
 */
#[JSONAPI\Resource(type: ResourceTypes::USER_ACCOUNTS, id: 'getId')]
final class UserAccountResource
{
    /**
     * @param UserAccountEntity $userAccount
     * @param ?UserEntity       $user
     */
    public function __construct(
        private readonly UserAccountEntity $userAccount,
        private readonly ?UserEntity $user = null
    ) {
    }

    public function getId(): string
    {
        return (string) $this->userAccount->getUser()->getUuid();
    }

    #[JSONAPI\Attribute(name: 'email')]
    public function getEmail(): string
    {
        return (string) $this->userAccount->getUser()->getEmailAddress();
    }

    #[JSONAPI\Attribute(name: 'username')]
    public function getUsername(): string
    {
        return (string) $this->userAccount->getUser()->getUsername();
    }

    #[JSONAPI\Attribute(name: 'remark')]
    public function getRemark(): ?string
    {
        return $this->userAccount->getUser()->getRemark();
    }

    #[JSONAPI\Attribute(name: 'last_login')]
    public function getLastLogin(): string
    {
        return (string) $this->userAccount->getLastLogin();
    }

    #[JSONAPI\Attribute(name: 'last_unsuccessful_login')]
    public function getLastUnsuccessfulLogin(): ?string
    {
        $timestamp = $this->userAccount->getLastUnsuccessFulLogin();
        if ($timestamp) {
            return (string) $timestamp;
        }
        return null;
    }

    #[JSONAPI\Attribute(name: 'revoked')]
    public function isRevoked(): bool
    {
        return $this->userAccount->isRevoked();
    }

    #[JSONAPI\Attribute(name: 'created_at')]
    public function getCreationDate(): string
    {
        return (string) $this->userAccount->getUser()->getTraceableTime()->getCreatedAt();
    }

    #[JSONAPI\Attribute(name: 'updated_at')]
    public function getUpdateDate(): ?string
    {
        return $this->userAccount->getUser()->getTraceableTime()->getUpdatedAt()?->__toString();
    }

    #[JSONAPI\Attribute(name: 'owner')]
    public function isOwner(): bool
    {
        if ($this->user) {
            return $this->userAccount->id() === $this->user->id();
        }
        return false;
    }
}
