<?php
/**
 * @package Applications
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Applications\Trainings\Resources;

use Kwai\Core\Domain\Entity;
use Kwai\JSONAPI;
use Kwai\Modules\Trainings\Domain\Team;

/**
 * Class TeamResource
 */
#[JSONAPI\Resource(type: 'teams', id: 'getId')]
class TeamResource
{
    /**
     * @param Entity<Team> $team
     */
    public function __construct(
        private Entity $team
    ) {
    }

    public function getId(): string
    {
        return (string) $this->team->id();
    }

    #[JSONAPI\Attribute(name: 'name')]
    public function getName(): string
    {
        return $this->team->getName();
    }
}
