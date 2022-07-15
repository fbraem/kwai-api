<?php

namespace Kwai\Modules\Trainings\Infrastructure\Mappers;

use Kwai\Core\Domain\ValueObjects\Date;
use Kwai\Core\Domain\ValueObjects\Gender;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Modules\Trainings\Domain\Member;
use Kwai\Modules\Trainings\Infrastructure\MembersTable;
use Kwai\Modules\Trainings\Infrastructure\PersonsTable;

class MemberDTO
{
    public function __construct(
        public MembersTable $member = new MembersTable(),
        public PersonsTable $person = new PersonsTable()
    ) {
    }
    
    public function create(): Member
    {
        return new Member(
            license: $this->member->license,
            licenseEndDate: Date::createFromString($this->member->license_end_date),
            name: new Name(
                $this->person->firstname,
                $this->person->lastname
            ),
            gender: Gender::from($this->person->gender),
            birthDate: Date::createFromString($this->person->birthdate)
        );
    }
}
