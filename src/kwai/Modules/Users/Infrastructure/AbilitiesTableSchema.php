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
 * Class AbilitiesTableSchema
 */
#[TableAttribute(name: 'abilities')]
class AbilitiesTableSchema extends TableSchema
{
    public ?int $id = null;
    public string $name;
    public ?string $remark = null;
    public string $created_at;
    public ?string $updated_at = null;
}
