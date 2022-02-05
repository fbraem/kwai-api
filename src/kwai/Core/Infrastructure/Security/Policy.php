<?php
/**
 * @package Core
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Core\Infrastructure\Security;

/**
 * Interface Policy
 *
 * An interface for implementing policies (Checking permissions)
 */
interface Policy
{
    /**
     * Return true, when a user is allowed to create an entity.
     *
     * @return bool
     */
    public function canCreate(): bool;

    /**
     * Return true, when a user is allowed to remove an entity.
     *
     * @return bool
     */
    public function canRemove(): bool;

    /**
     * Return true, when a user is allowed to view an entity.
     *
     * @return bool
     */
    public function canView(): bool;

    /**
     * Return true, when a user is allowed to update an entity.
     *
     * @return bool
     */
    public function canUpdate(): bool;
}
