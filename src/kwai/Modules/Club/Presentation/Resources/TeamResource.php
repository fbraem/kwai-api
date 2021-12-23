<?php
/**
 * @package Modules
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Modules\Club\Presentation\Resources;

use Kwai\Core\Domain\Entity;
use Kwai\JSONAPI;
use Kwai\Modules\Club\Domain\Team;

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

    #[JSONAPI\Attribute(name: 'created_at')]
    public function getCreatedAt(): string
    {
        return (string) $this->team->getTraceableTime()->getCreatedAt();
    }

    #[JSONAPI\Attribute(name: 'updated_at')]
    public function getUpdatedAt(): ?string
    {
        return $this->team->getTraceableTime()->getUpdatedAt()?->__toString();
    }
}
