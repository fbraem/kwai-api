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
 * Class AbilityRulesTableSchema
 */
#[TableAttribute(name: 'ability_rules')]
class AbilityRulesTableSchema extends TableSchema
{
    public ?int $ability_id = null;
    public ?int $rule_id = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;
}
