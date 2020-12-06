<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Modules\Trainings\Domain\Season;

/**
 * Class SeasonMapper
 */
class SeasonMapper
{
    /**
     * Maps a table record to the Season domain entity
     *
     * @param Collection $data
     * @return Entity
     */
    public static function toDomain(Collection $data): Entity
    {
        return new Entity(
            (int) $data->get('id'),
            new Season(
                name: $data->get('name')
            )
        );
    }
}
