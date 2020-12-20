<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\UseCases;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Creator;
use Kwai\Core\Domain\ValueObjects\DocumentFormat;
use Kwai\Core\Domain\ValueObjects\Event;
use Kwai\Core\Domain\ValueObjects\Locale;
use Kwai\Core\Domain\ValueObjects\Location;
use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Exceptions\CoachNotFoundException;
use Kwai\Modules\Trainings\Domain\Exceptions\DefinitionNotFoundException;
use Kwai\Modules\Trainings\Domain\Exceptions\TrainingNotFoundException;
use Kwai\Modules\Trainings\Domain\Training;
use Kwai\Modules\Trainings\Domain\ValueObjects\TrainingCoach;
use Kwai\Modules\Trainings\Repositories\CoachRepository;
use Kwai\Modules\Trainings\Repositories\DefinitionRepository;
use Kwai\Modules\Trainings\Repositories\TeamRepository;
use Kwai\Modules\Trainings\Repositories\TrainingRepository;

/**
 * Class UpdateTraining
 *
 * Use case for updating a training
 */
class UpdateTraining
{
    /**
     * UpdateTraining constructor.
     *
     * @param TrainingRepository   $trainingRepo
     * @param DefinitionRepository $definitionRepository
     * @param TeamRepository       $teamRepository
     * @param CoachRepository      $coachRepository
     */
    public function __construct(
        private TrainingRepository $trainingRepo,
        private DefinitionRepository $definitionRepository,
        private TeamRepository $teamRepository,
        private CoachRepository $coachRepository
    ) {
    }

    /**
     * Factory method
     *
     * @param TrainingRepository   $trainingRepo
     * @param DefinitionRepository $definitionRepository
     * @param TeamRepository       $teamRepository
     * @param CoachRepository      $coachRepository
     * @return UpdateTraining
     */
    public static function create(
        TrainingRepository $trainingRepo,
        DefinitionRepository $definitionRepository,
        TeamRepository $teamRepository,
        CoachRepository $coachRepository
    ) {
        return new self(
            $trainingRepo,
            $definitionRepository,
            $teamRepository,
            $coachRepository
        );
    }

    /**
     * @param UpdateTrainingCommand $command
     * @param Creator               $creator
     * @return Entity<Training>
     * @throws DefinitionNotFoundException
     * @throws QueryException
     * @throws RepositoryException
     * @throws TrainingNotFoundException
     * @throws CoachNotFoundException
     */
    public function __invoke(UpdateTrainingCommand $command, Creator $creator): Entity
    {
        $training = $this->trainingRepo->getById($command->id);

        // Handle definition
        $definition = isset($command->definition_id)
            ? $this->definitionRepository->getById($command->definition_id)
            : null
        ;

        // Handle coaches
        $trainingCoaches = collect($command->coaches);
        if ($trainingCoaches->isNotEmpty()) {
            $coachIds = $trainingCoaches->map(fn($item) => $item->id);
            $coaches = $this->coachRepository->getById(...$coachIds);
            $diff = $coachIds->diff($coaches->keys());
            if ($diff->count() > 0) {
                // For now throw for the first coach which is not found
                throw new CoachNotFoundException($diff->first());
            }
            $trainingCoaches = $trainingCoaches->transform(
                fn($coach) =>
                new TrainingCoach(
                    coach: $coaches->get($coach->id),
                    present: $coach->present,
                    head: $coach->head,
                    traceableTime: TraceableTime::createFrom($training->getCoaches()->get($coach->id)?->getTraceableTime()),
                    creator: $creator,
                    payed: $coach->payed,
                    remark: $coach->remark
                )
            );
        }

        // Handle teams
        $teams = count($command->teams) > 0
            ? $this->teamRepository->getById(...$command->teams)
            : new Collection()
        ;

        $contents = new Collection();
        foreach($command->contents as $text) {
            $contents->add(new Text(
                new Locale($text->locale),
                new DocumentFormat($text->format),
                $text->title,
                $text->summary,
                $text->content ?? null,
                $creator
            ));
        }

        $entity = new Entity(
            $command->id,
            new Training(
                event: new Event(
                    startDate: Timestamp::createFromString($command->start_date, $command->timezone),
                    endDate: Timestamp::createFromString($command->end_date, $command->timezone),
                    location: $command->location ? new Location($command->location) : null,
                    active: $command->active,
                    cancelled: $command->cancelled
                ),
                text: $contents,
                traceableTime: TraceableTime::createFrom($training->getTraceableTime()),
                definition: $definition,
                coaches: $trainingCoaches,
                teams: $teams,
                remark: $command->remark,
            )
        );
        $this->trainingRepo->update($entity);

        return $entity;
    }
}
