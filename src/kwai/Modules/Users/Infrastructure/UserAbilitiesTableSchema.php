<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Infrastructure;

use Kwai\Core\Infrastructure\Database\TableAttribute;
use Kwai\Core\Infrastructure\Database\TableSchema;

/**
 * Class UserAbilitiesTableSchema
 */
#[TableAttribute(name: 'user_abilities')]
class UserAbilitiesTableSchema extends TableSchema
{
    public ?int $user_id = null;
    public ?int $ability_id = null;
}
