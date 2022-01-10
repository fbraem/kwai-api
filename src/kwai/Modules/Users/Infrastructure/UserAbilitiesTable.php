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
 * Class UserAbilitiesTable
 */
#[TableAttribute(name: 'user_abilities')]
class UserAbilitiesTable extends TableSchema
{
    public ?int $user_id = null;
    public ?int $ability_id = null;
}
