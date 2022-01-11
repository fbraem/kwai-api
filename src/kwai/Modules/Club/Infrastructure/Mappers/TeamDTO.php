<?php
/**
 * @package Modules
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Modules\Club\Infrastructure\Mappers;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Infrastructure\Mappers\DataTransferObject;
use Kwai\Modules\Club\Domain\Team;
use Kwai\Modules\Club\Infrastructure\TeamsTable;

/**
 * Class TeamDTO
 *
 * Data transfer object for the Teams entity and the teams table.
 */
final class TeamDTO implements DataTransferObject
{
    public function __construct(
        public TeamsTable $team
    ) {
    }

    /**
     * Create a Team domain object from a database row.
     *
     * @return Team
     */
    public function create(): Team
    {
        return new Team(
            name: $this->team->name,
            traceableTime: new TraceableTime(
                Timestamp::createFromString($this->team->created_at),
                $this->team->updated_at
                    ? Timestamp::createFromString($this->team->updated_at)
                    : null
            )
        );
    }

    /**
     * Create a Team entity from a database row.
     *
     * @return Entity<Team>
     */
    public function createEntity(): Entity
    {
        return new Entity(
            $this->team->id,
            $this->create()
        );
    }
}
