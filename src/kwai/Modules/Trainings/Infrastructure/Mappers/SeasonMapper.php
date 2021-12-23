<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Mappers;

use Illuminate\Support\Collection;
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
     * @return Season
     */
    public static function toDomain(Collection $data): Season
    {
        return new Season(
            name: $data->get('name')
        );
    }
}
