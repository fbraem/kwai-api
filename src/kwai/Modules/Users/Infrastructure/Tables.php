<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure;

use Closure;
use Kwai\Core\Infrastructure\Database\ColumnFilter;
use Kwai\Core\Infrastructure\Database\TableEnum;
use MyCLabs\Enum\Enum;
use function Latitude\QueryBuilder\alias;

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
    public const ABILITIES = 'abilities';
    public const ABILITY_RULES = 'ability_rules';
    public const ACCESS_TOKENS = 'oauth_access_tokens';
    public const REFRESH_TOKENS = 'oauth_refresh_tokens';
    public const RULES = 'rules';
    public const RULE_ACTIONS = 'rule_actions';
    public const RULE_SUBJECTS = 'rule_subjects';
    public const USER_ABILITIES = 'user_abilities';
    public const USERS = 'users';
    public const USER_INVITATIONS = 'user_invitations';
}
