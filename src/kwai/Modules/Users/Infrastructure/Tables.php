<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure;

use Kwai\Core\Infrastructure\Database\TableTrait;

/**
 * Enum Tables
 */
enum Tables: string
{
    case ABILITIES = 'abilities';
    case ABILITY_RULES = 'ability_rules';
    case ACCESS_TOKENS = 'oauth_access_tokens';
    case REFRESH_TOKENS = 'oauth_refresh_tokens';
    case RULES = 'rules';
    case RULE_ACTIONS = 'rule_actions';
    case RULE_SUBJECTS = 'rule_subjects';
    case USER_ABILITIES = 'user_abilities';
    case USERS = 'users';
    case USER_INVITATIONS = 'user_invitations';

    use TableTrait;
}
