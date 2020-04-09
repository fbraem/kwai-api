<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure;

/**
 * Class Tables
 *
 * This class defines all table names for the Users module.
 */
class Tables
{
    public const ABILITIES = 'abilities';
    public const ABILITY_RULES = 'ability_rules';
    public const ACCESS_TOKENS = 'oauth_access_tokens';
    public const REFRESH_TOKENS = 'oauth_refresh_tokens';
    public const RULES = 'rules';
    public const RULE_ACTIONS = 'rule_actions';
    public const RULE_SUBJECTS = 'rule_subjects';
    public const USER_ABILITIES = 'user_abilities';
    public const USERS = 'users';
}
