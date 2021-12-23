<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Presentation\Resources;

use Kwai\Core\Domain\Entity;
use Kwai\JSONAPI;
use Kwai\Modules\Coaches\Domain\Coach;

/**
 * Class CoachResource
 */
#[JSONAPI\Resource(type: 'coaches', id: 'getId')]
class CoachResource
{
    /**
     * @param Entity<Coach> $coach
     */
    public function __construct(
        private Entity $coach,
        private ?Entity $user = null
    ) {
    }

    public function getId(): string
    {
        return (string) $this->coach->id();
    }

    #[JSONAPI\Attribute(name: 'bio')]
    public function getBio(): ?string
    {
        return $this->coach->getBio();
    }

    #[JSONAPI\Attribute(name: 'diploma')]
    public function getDiploma(): ?string
    {
        return $this->coach->getDiploma();
    }

    #[JSONAPI\Attribute(name: 'active')]
    public function isActive(): bool
    {
        return $this->coach->isActive();
    }

    #[JSONAPI\Attribute(name: 'remark')]
    public function getRemark(): ?string
    {
        return $this->coach->getRemark();
    }

    #[JSONAPI\Attribute(name: 'owner')]
    public function isOwner(): bool
    {
        if ($this->user) {
            return $this->coach->getUser()?->id() === $this->user->id();
        }
        return false;
    }

    #[JSONAPI\Attribute(name: 'created_at')]
    public function getCreatedAt(): string
    {
        return (string) $this->coach->getTraceableTime()->getCreatedAt();
    }

    #[JSONAPI\Attribute(name: 'updated_at')]
    public function getUpdatedAt(): ?string
    {
        return $this->coach->getTraceableTime()->getUpdatedAt()?->__toString();
    }

    #[JSONAPI\Relationship(name: 'member')]
    public function getMember(): MemberResource
    {
        return new MemberResource($this->coach->getMember());
    }
}
