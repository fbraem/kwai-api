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
 * Class CoachesTable
 */
#[TableAttribute(name: 'coaches')]
final class CoachesTable extends TableSchema
{
    public ?int $id = null;
    public ?int $active;
}
