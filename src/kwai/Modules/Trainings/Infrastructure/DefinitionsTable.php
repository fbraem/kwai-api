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
 * Class DefinitionsTable
 */
#[TableAttribute(name: 'training_definitions')]
final class DefinitionsTable extends TableSchema
{
    public ?int $id = null;
    public string $name;
    public string $description;
    public ?int $season_id = null;
    public ?int $team_id = null;
    public int $weekday;
    public string $start_time;
    public string $end_time;
    public string $time_zone;
    public int $active;
    public ?string $location = null;
    public ?string $remark = null;
    public ?int $user_id = null;
    public string $created_at;
    public ?string $updated_at = null;
}
