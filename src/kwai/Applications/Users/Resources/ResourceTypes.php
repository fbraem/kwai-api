<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Applications\Users\Resources;

/**
 * Enum ResourceTypes
 *
 * All types for JSONAPI resources.
 */
enum ResourceTypes: string
{
    const USER_ACCOUNTS = 'user_accounts';
    const USER_INVITATIONS = 'user_invitations';
    const USER_RECOVERIES = 'user_recoveries';
    const USERS = 'users';
}
