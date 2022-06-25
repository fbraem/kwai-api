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
 * Class TrainingCoachesTable
 */
#[TableAttribute(name: 'training_coaches')]
class TrainingCoachesTable extends TableSchema
{
    public int $training_id;
    public int $coach_id;
    public int $coach_type;
    public int $payed;
    public int $present;
    public ?string $remark;
    public int $user_id;
    public ?string $updated_at = null;
    public string $created_at;
}
