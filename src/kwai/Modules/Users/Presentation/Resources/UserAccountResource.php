<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Presentation\Resources;

use Kwai\Core\Domain\Entity;
use Kwai\JSONAPI;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\UserAccount;

/**
 * Class UserAccountResource
 */
#[JSONAPI\Resource(type: 'user_accounts', id: 'getId')]
class UserAccountResource
{
    /**
     * @param Entity<UserAccount> $userAccount
     * @param ?Entity<User> $user
     */
    public function __construct(
        private Entity $userAccount,
        private ?Entity $user = null
    ) {
    }

    public function getId(): string
    {
        return (string) $this->userAccount->id();
    }

    #[JSONAPI\Attribute(name: 'uuid')]
    public function getUuid(): string
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
