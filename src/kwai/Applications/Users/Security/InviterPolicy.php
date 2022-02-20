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

/**
 * Class InviterPolicy
 *
 * A policy for an inviter.
 */
class InviterPolicy implements Policy
{
    /**
     * @param ?Entity<User> $user
     */
    public function __construct(
        private ?Entity $user = null
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
        return $this->user?->hasRole('admin') ?? false;
    }

    /**
     * @inheritDoc
     */
    public function canView(): bool
    {
        return $this->user?->hasRole('admin') ?? false;
    }

    /**
     * @inheritDoc
     */
    public function canUpdate(): bool
    {
        return $this->user?->hasRole('admin') ?? false;
    }
}
