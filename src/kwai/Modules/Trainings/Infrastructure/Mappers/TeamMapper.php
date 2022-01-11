<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Modules\Trainings\Domain\Team;

/**
 * Class TeamDTO
 */
class TeamMapper
{
    /**
     * Maps a table record to the Team domain entity
     *
     * @param Collection $data
     * @return Team
     */
    public static function toDomain(Collection $data): Team
    {
        return new Team(
            name: $data->get('name')
        );
    }
}
