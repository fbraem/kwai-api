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
 * Class TrainingContentsTable
 */
#[TableAttribute(name: 'training_contents')]
class TrainingContentsTable extends TableSchema
{
    public int $training_id;
    public string $locale;
    public string $format;
    public string $title;
    public ?string $content;
    public string $summary;
    public int $user_id;
    public ?string $updated_at = null;
    public string $created_at;
}
