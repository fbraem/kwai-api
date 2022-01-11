<?php
/**
 * @package Modules
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Modules\Club\Infrastructure\Mappers;

use Kwai\Core\Domain\DomainEntity;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Infrastructure\Mappers\DataTransferObject;
use Kwai\Modules\Club\Domain\Member;
use Kwai\Modules\Club\Infrastructure\ContactsTable;
use Kwai\Modules\Club\Infrastructure\MembersTable;
use Kwai\Modules\Club\Infrastructure\PersonsTable;

/**
 * Class MemberDTO
 */
class MemberDTO implements DataTransferObject
{
    public function __construct(
        public MembersTable  $member,
        public ContactsTable $contact,
        public PersonsTable  $person
    ) {
    }

    /**
     * Create a Member domain object from a database row
     *
     * @return Member
     */
    public function create(): Member
    {
        return new Member(
            new Name(
                $this->person->firstname,
                $this->person->lastname
            )
        );
    }

    /**
     * Create a Member entity from a database row.
     *
     * @return Entity<Member>
     */
    public function createEntity(): Entity
    {
        return new Entity(
            $this->member->id,
            $this->create()
        );
    }
}
