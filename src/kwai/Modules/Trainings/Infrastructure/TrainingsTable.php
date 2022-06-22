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
 * Class TrainingsTable
 */
#[TableAttribute(name: 'trainings')]
final class TrainingsTable extends TableSchema
{
    public ?int $id;
    public ?int $definition_id = null;
    public ?int $season_id = null;
    public string $created_at;
    public ?string $updated_at = null;
    public string $start_date;
    public string $end_date;
    public string $time_zone;
    public int $active;
    public int $cancelled;
    public ?string $location = null;
    public ?string $remark = null;
}
