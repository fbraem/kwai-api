<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\Event;
use Kwai\Core\Domain\ValueObjects\Location;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Modules\Trainings\Domain\Training;
use Kwai\Modules\Trainings\Domain\TrainingEntity;
use Kwai\Modules\Trainings\Infrastructure\TeamsTable;
use Kwai\Modules\Trainings\Infrastructure\TrainingContentsTable;
use Kwai\Modules\Trainings\Infrastructure\TrainingsTable;
use Kwai\Modules\Trainings\Infrastructure\DefinitionsTable;
use Kwai\Modules\Trainings\Infrastructure\SeasonsTable;


/**
 * Class TrainingDTO
 */
class TrainingDTO
{
    /**
     * @param TrainingsTable                    $training
     * @param Collection<TrainingContentsTable> $contents
     * @param DefinitionsTable                  $definition
     * @param SeasonsTable                      $season
     * @param Collection<TrainingCoachDTO>      $coaches
     * @param Collection<TeamsTable>            $teams
     */
    public function __construct(
        public TrainingsTable $training = new TrainingsTable(),
        public Collection $contents = new Collection(),
        public DefinitionsTable $definition = new DefinitionsTable(),
        public SeasonsTable $season = new SeasonsTable(),
        public Collection $coaches = new Collection(),
        public Collection $teams = new Collection()
    ) {
    }

    public function create(): Training
    {
        return new Training(
            event: new Event(
                startDate: Timestamp::createFromString($this->training->start_date),
                endDate: Timestamp::createFromString($this->training->end_date),
                timezone: $this->training->time_zone,
                location: $this->training->location ? new Location($this->training->location) : null,
                active: $this->training->active === 1,
                cancelled: $this->training->cancelled === 1
            ),
            text: new Collection(),
            remark: $this->training->remark,
            traceableTime: new TraceableTime(
                Timestamp::createFromString($this->training->created_at),
                $this->training->updated_at
                    ? Timestamp::createFromString($this->training->updated_at)
                    : null
            ),
            coaches: $this->coaches->map(static fn(TrainingCoachDTO $dto) => $dto->create())
        );
    }

    public function createEntity(): TrainingEntity
    {
        return new TrainingEntity(
            $this->training->id,
            $this->create()
        );
    }

    public function persist(Training $training): TrainingDTO
    {
        $this->training->definition_id = $training->getDefinition()?->id();
        // $this->training->season_id = $training->getSeason()?->id();
        $this->training->location = $training->getEvent()->getLocation()?->__toString();
        $this->training->remark = $training->getRemark();
        $this->training->cancelled = $training->getEvent()->isCancelled() ? 1 : 0;
        $this->training->active = $training->getEvent()->isActive() ? 1 : 0;
        $this->training->time_zone = $training->getEvent()->getTimezone();
        $this->training->end_date = (string) $training->getEvent()->getEndDate();
        $this->training->start_date = (string) $training->getEvent()->getStartDate();
        $this->training->created_at = (string) $training->getTraceableTime()->getCreatedAt();
        if ($training->getTraceableTime()->isUpdated()) {
            $this->training->updated_at = (string) $training->getTraceableTime()->getUpdatedAt();
        }
        return $this;
    }

    public function persistEntity(TrainingEntity $training): TrainingDTO
    {
        $this->training->id = $training->id();
        return $this->persist($training->domain());
    }
}
