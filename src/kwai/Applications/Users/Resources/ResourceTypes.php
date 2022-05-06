<?php
/**
 * @package Applications
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Applications\Users\Resources;

use Doctrine\Common\Annotations\Annotation\Enum;

/**
 * Enum ResourceTypes
 *
 * All types for JSONAPI resources.
 */
enum ResourceTypes: string
{
    const ROLES = 'roles';
    const RULES = 'rules';
    const USER_ACCOUNTS = 'user_accounts';
    const USER_INVITATIONS = 'user_invitations';
    const USERS = 'users';
}
