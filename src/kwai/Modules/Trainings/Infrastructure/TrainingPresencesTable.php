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
 * Class TrainingPresencesTable
 *
 * Represents the training_presences table.
 */
#[TableAttribute(name: 'training_presences')]
final class TrainingPresencesTable extends TableSchema
{
    public ?int $training_id = null;
    public ?int $member_id = null;
    public ?string $remark = null;
    public ?int $user_id = null;
    public string $created_at;
    public ?string $updated_at = null;
}
