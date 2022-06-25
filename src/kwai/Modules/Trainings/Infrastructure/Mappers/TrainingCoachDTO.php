<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Mappers;

use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Modules\Trainings\Domain\Coach;
use Kwai\Modules\Trainings\Domain\CoachEntity;
use Kwai\Modules\Trainings\Domain\ValueObjects\TrainingCoach;
use Kwai\Modules\Trainings\Infrastructure\MembersTable;
use Kwai\Modules\Trainings\Infrastructure\PersonsTable;
use Kwai\Modules\Trainings\Infrastructure\TrainingCoachesTable;
use Kwai\Modules\Trainings\Infrastructure\UsersTable;

/**
 * Class TrainingCoachDTO
 */
class TrainingCoachDTO
{
    public function __construct(
        public TrainingCoachesTable $trainingCoach = new TrainingCoachesTable(),
        public MembersTable $member = new MembersTable(),
        public PersonsTable $person = new PersonsTable(),
        public UsersTable $user = new UsersTable()
    ) {
    }

    public function create(): TrainingCoach
    {
        return new TrainingCoach(
            coach: new CoachEntity(
                $this->trainingCoach->coach_id,
                new Coach(
                    new Name(
                        $this->person->firstname,
                        $this->person->lastname
                    )
                )
            ),
            creator: new Creator(
                $this->user->id,
                new Name(
                    $this->user->first_name,
                    $this->user->last_name
                )
            ),
            head: $this->trainingCoach->coach_type === 1,
            present: $this->trainingCoach->present === 1,
            payed: $this->trainingCoach->payed === 1,
            remark: $this->trainingCoach->remark,
            traceableTime: new TraceableTime(
                Timestamp::createFromString($this->trainingCoach->created_at),
                $this->trainingCoach->updated_at
                    ? Timestamp::createFromString($this->trainingCoach->updated_at)
                    : null
            )
        );
    }

    public function persist(TrainingCoach $coach): TrainingCoachDTO
    {
        $this->trainingCoach->user_id = $coach->getCreator()->getId();
        $this->trainingCoach->coach_id = $coach->getCoach()->id();
        $this->trainingCoach->present = $coach->isPresent() ? 1 : 0;
        $this->trainingCoach->coach_type = $coach->isHead() ? 1 : 0;
        $this->trainingCoach->payed = $coach->isPayed() ? 1 : 0;
        $this->trainingCoach->remark = $coach->getRemark();
        $this->trainingCoach->created_at = (string) $coach->getTraceableTime()->getCreatedAt();
        if ($coach->getTraceableTime()->isUpdated()) {
            $this->trainingCoach->updated_at = (string) $coach->getTraceableTime()->getUpdatedAt();
        } else {
            $this->trainingCoach->updated_at = null;
        }
        return $this;
    }
}
