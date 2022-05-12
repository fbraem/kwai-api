<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Applications\Users\Security;

use Kwai\Core\Infrastructure\Security\Policy;
use Kwai\Modules\Users\Domain\UserEntity;
use Kwai\Modules\Users\Domain\UserInvitationEntity;

/**
 * Class InviterPolicy
 *
 * A policy for an inviter.
 */
class InviterPolicy implements Policy
{
    /**
     * @param ?UserEntity $user
     * @param ?UserInvitationEntity   $invitation
     */
    public function __construct(
        private ?UserEntity $user = null,
        private ?UserInvitationEntity $invitation = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function canCreate(): bool
    {
        return $this->user?->isAdmin() ?? false;
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
        if ($this->user->isAdmin()) {
            return true;
        }
        if ($this->invitation) {
            return $this->user->id() === $this->invitation->getCreator()->getId();
        }
        return false;
    }

    /**
     * @inheritDoc
     */
    public function canUpdate(): bool
    {
        return $this->canView();
    }
}
