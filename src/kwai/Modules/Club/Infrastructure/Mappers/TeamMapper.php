<?php
/**
 * @package Modules
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Modules\Club\Infrastructure\Mappers;

use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Modules\Club\Domain\Team;
use Kwai\Modules\Club\Infrastructure\TeamsTableSchema;

/**
 * Class TeamMapper
 */
class TeamMapper
{
    public static function toDomain(TeamsTableSchema $schema): Team
    {
        return new Team(
            name: $schema->name,
            traceableTime: new TraceableTime(
                Timestamp::createFromString($schema->created_at),
                $schema->updated_at
                    ? Timestamp::createFromString($schema->updated_at)
                    : null
            )
        );
    }
}
