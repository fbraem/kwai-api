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
 * Class TrainingTeamsTable
 */
#[TableAttribute(name: 'training_teams')]
final class TrainingTeamsTable extends TableSchema
{
    public int $training_id;
    public int $team_id;
}
