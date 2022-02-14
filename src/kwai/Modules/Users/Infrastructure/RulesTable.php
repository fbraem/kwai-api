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
 * Class RulesTable
 */
#[TableAttribute(name: 'rules')]
class RulesTable extends TableSchema
{
    public int $id;
    public string $name;
    public int $action_id;
    public int $subject_id;
    public int $owner;
    public int $permission;
    public ?string $remark;
    public string $created_at;
    public ?string $updated_at;
}