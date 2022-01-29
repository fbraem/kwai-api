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
 * Class RolesTable
 */
#[TableAttribute(name: 'roles')]
class RolesTable extends TableSchema
{
    public ?int $id = null;
    public string $name;
    public ?string $remark = null;
    private ?string $description;
    public string $created_at;
    public ?string $updated_at = null;
}
