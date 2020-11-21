<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Mappers;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Modules\Trainings\Domain\Coach;
use Kwai\Modules\Trainings\Domain\ValueObjects\Creator;

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
                'description' => $raw->description,
                'diploma' => $raw->diploma,
                'active' => $raw->active == '1' ?? true,
                'remark' => $raw->remark,
                'traceableTime' => new TraceableTime(
                    Timestamp::createFromString($raw->created_at),
                    isset($raw->updated_at)
                        ? Timestamp::createFromString($raw->updated_at)
                        : null
                ),
                'creator' => new Creator(
                    (int) $raw->creator->id,
                    new Name(
                        $raw->creator->first_name,
                        $raw->creator->last_name
                    )
                ),
                'member' => MemberMapper::toDomain($raw->member)
            ])
        );
    }
}
