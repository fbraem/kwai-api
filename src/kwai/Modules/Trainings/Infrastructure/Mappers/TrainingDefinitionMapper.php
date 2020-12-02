<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Modules\Trainings\Domain\TrainingDefinition;

/**
 * Class TrainingDefinitionMapper
 */
class TrainingDefinitionMapper
{
    /**
     * Maps a table record to the Training domain entity.
     *
     * @param Collection $data
     * @return Entity<TrainingDefinition>
     */
    public static function toDomain(Collection $data): Entity
    {
        return new Entity(
            (int)$data['id'],
            new TrainingDefinition((object)[
                'name' => $data->get('name')
            ])
        );
    }
}
