<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure;

use Kwai\Core\Infrastructure\Database\TableAttribute;
use Kwai\Core\Infrastructure\Database\TableSchema;

/**
 * Class UsersTable
 */
#[TableAttribute(name: 'users')]
final class UsersTable extends TableSchema
{
    public ?int $id = null;
    public string $first_name;
    public string $last_name;
}
