<?php
/**
 * @package Modules
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Modules\Club\Presentation\Resources;

use Kwai\Core\Domain\Entity;
use Kwai\JSONAPI;
use Kwai\Modules\Club\Domain\Member;

/**
 * Class MemberResource
 */
#[JSONAPI\Resource(type: 'members', id: 'getId')]
class MemberResource
{
    /**
     * @param Entity<Member> $member
     */
    public function __construct(
        private Entity $member
    ) {
    }

    public function getId(): string
    {
        return (string) $this->member->id();
    }

    #[JSONAPI\Attribute(name: 'name')]
    public function getName(): string
    {
        return (string) $this->member->getName();
    }

    #[JSONAPI\Attribute(name: 'created_at')]
    public function getCreatedAt(): string
    {
        return (string) $this->member->getTraceableTime()->getCreatedAt();
    }

    #[JSONAPI\Attribute(name: 'updated_at')]
    public function getUpdatedAt(): ?string
    {
        return $this->member->getTraceableTime()->getUpdatedAt()?->__toString();
    }
}
