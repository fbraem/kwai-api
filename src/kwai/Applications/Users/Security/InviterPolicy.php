<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Applications\Users\Security;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Security\Policy;
use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\UserInvitation;

/**
 * Class InviterPolicy
 *
 * A policy for an inviter.
 */
class InviterPolicy implements Policy
{
    /**
     * @param ?Entity<User> $user
     * @param ?Entity<UserInvitation>   $invitation
     */
    public function __construct(
        private ?Entity $user = null,
        private ?Entity $invitation = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function canCreate(): bool
    {
        return $this->user?->hasRole('admin') ?? false;
    }

    /**
     * @inheritDoc
     */
    public function canRemove(): bool
    {
        return $this->canView();
    }

    /**
     * @inheritDoc
     */
    public function canView(): bool
    {
        if ($this->user === null) {
            return false;
        }
        if ($this->user->hasRole('admin')) {
            return true;
        }
        return $this->user->id() === $this->invitation->getCreator()->getId();
    }

    /**
     * @inheritDoc
     */
    public function canUpdate(): bool
    {
        return $this->canView();
    }
}
