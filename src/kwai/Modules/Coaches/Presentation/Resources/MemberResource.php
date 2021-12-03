<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Presentation\Resources;

use Kwai\Core\Domain\Entity;
use Kwai\JSONAPI;
use Kwai\Modules\Coaches\Domain\Member;

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
}
