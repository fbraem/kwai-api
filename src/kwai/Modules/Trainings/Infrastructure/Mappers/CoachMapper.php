<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Mappers;

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
     * @param object $raw
     * @return Entity<Coach>
     */
    public static function toDomain(object $raw): Entity
    {
        return new Entity(
            (int) $raw->id,
            new Coach((object) [
                'name' => new Name($raw->firstname, $raw->lastname)
            ])
        );
    }
}
