<?php
/**
 * @package Applications
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Applications\Trainings\Resources;

use Kwai\JSONAPI;
use Kwai\Modules\Trainings\Domain\TeamEntity;

/**
 * Class TeamResource
 */
#[JSONAPI\Resource(type: 'teams', id: 'getId')]
class TeamResource
{
    /**
     * @param TeamEntity $team
     */
    public function __construct(
        private TeamEntity $team
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
