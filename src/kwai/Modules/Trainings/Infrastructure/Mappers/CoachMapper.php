<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Modules\Trainings\Domain\Coach;

/**
 * Class CoachMapper
 */
class CoachMapper
{
    /**
     * Maps a table record to the Coach domain entity.
     *
     * @param Collection $data
     * @return Coach
     */
    public static function toDomain(Collection $data): Coach
    {
        return new Coach(
            name: new Name(
                $data->get('firstname'),
                $data->get('lastname')
            )
        );
    }
}
