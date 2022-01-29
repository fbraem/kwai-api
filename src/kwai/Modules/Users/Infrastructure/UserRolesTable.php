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
 * Class UserRolesTable
 */
#[TableAttribute(name: 'user_roles')]
class UserRolesTable extends TableSchema
{
    public int $user_id;
    public int $role_id;
}
