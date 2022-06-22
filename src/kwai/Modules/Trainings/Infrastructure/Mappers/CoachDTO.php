<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Modules\Trainings\Domain\Coach;
use Kwai\Modules\Trainings\Domain\CoachEntity;
use Kwai\Modules\Trainings\Infrastructure\CoachesTable;
use Kwai\Modules\Trainings\Infrastructure\MembersTable;
use Kwai\Modules\Trainings\Infrastructure\PersonsTable;

/**
 * Class CoachDTO
 */
final class CoachDTO
{
    public function __construct(
        public CoachesTable $coachTable = new CoachesTable(),
        public MembersTable $membersTable = new MembersTable(),
        public PersonsTable $personsTable = new PersonsTable()
    ) {
    }

    public function create(): Coach
    {
        return new Coach(
            name: new Name(
                $this->personsTable->firstname,
                $this->personsTable->lastname
            )
        );
    }

    public function createEntity(): CoachEntity
    {
        return new CoachEntity(
            $this->coachTable->id,
            $this->create()
        );
    }
}
