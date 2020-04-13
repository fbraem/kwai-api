<?php
/**
 * @package Kwai
 * @subpackage Users
 */


/* @noinspection PhpUnusedPrivateFieldInspection */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure;

use Kwai\Core\Infrastructure\Database\TableEnum;

/**
 * Class Tables
 *
 * This class defines all table names for the Users module.
 * @method static Tables ABILITIES()
 * @method static Tables ABILITY_RULES()
 * @method static Tables ACCESS_TOKENS()
 * @method static Tables REFRESH_TOKENS()
 * @method static Tables RULES()
 * @method static Tables RULE_ACTIONS()
 * @method static Tables RULE_SUBJECTS()
 * @method static Tables USER_ABILITIES()
 * @method static Tables USERS()
 * @method static Tables USER_INVITATIONS()
 */
class Tables extends TableEnum
{
    private const ABILITIES = 'abilities';
    private const ABILITY_RULES = 'ability_rules';
    private const ACCESS_TOKENS = 'oauth_access_tokens';
    private const REFRESH_TOKENS = 'oauth_refresh_tokens';
    private const RULES = 'rules';
    private const RULE_ACTIONS = 'rule_actions';
    private const RULE_SUBJECTS = 'rule_subjects';
    private const USER_ABILITIES = 'user_abilities';
    private const USERS = 'users';
    private const USER_INVITATIONS = 'user_invitations';
}
