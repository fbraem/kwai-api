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
 * Class RoleRulesTable
 */
#[TableAttribute(name: 'role_rules')]
class RoleRulesTable extends TableSchema
{
    public ?int $role_id = null;
    public ?int $rule_id = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;
}
