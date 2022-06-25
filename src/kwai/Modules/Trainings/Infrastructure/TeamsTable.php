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
 * Class TeamsTable
 */
#[TableAttribute(name: 'teams')]
final class TeamsTable extends TableSchema
{
    public ?int $id;
    public ?string $name;
}
