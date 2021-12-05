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
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Exceptions\DefinitionNotFoundException;
use Kwai\Modules\Trainings\Domain\Training;
use Kwai\Modules\Trainings\Domain\ValueObjects\TrainingCoach;
use Kwai\Modules\Trainings\Repositories\CoachRepository;
use Kwai\Modules\Trainings\Repositories\DefinitionRepository;
use Kwai\Modules\Trainings\Repositories\TeamRepository;
use Kwai\Modules\Trainings\Repositories\TrainingRepository;

/**
 * Class CreateTraining
 *
 * Use case for creating
 */
class CreateTraining
{
    /**
     * CreateTraining constructor.
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
     * @return CreateTraining
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
     * @param CreateTrainingCommand $command
     * @param Creator               $creator
     * @return Entity<Training>
     * @throws RepositoryException
     * @throws DefinitionNotFoundException
     */
    public function __invoke(CreateTrainingCommand $command, Creator $creator): Entity
    {
        $definition = isset($command->definition)
            ? $this->definitionRepository->getById($command->definition)
            : null
        ;

        $teams = count($command->teams) > 0
            ? $this->teamRepository->getById(...$command->teams)
            : new Collection()
        ;

        $coachCollection = new Collection($command->coaches);
        $coaches = $coachCollection->isNotEmpty()
            ? $this->coachRepository->getById(
                ... $coachCollection->map(fn ($coach) => $coach->id)->toArray()
            ) : new Collection()
        ;
        $trainingCoaches = $coachCollection->map(
            fn ($coach) => new TrainingCoach(
                coach: $coaches->get($coach->id),
                creator: $creator,
                present: $coach->present,
                head: $coach->head,
                payed: $coach->payed
            )
        );

        $contents = new Collection();
        foreach ($command->contents as $text) {
            $contents->add(new Text(
                locale: new Locale($text->locale),
                format: new DocumentFormat($text->format),
                title: $text->title,
                summary: $text->summary,
                content: $text->content ?? null,
                author: $creator
            ));
        }

        return $this->trainingRepo->create(new Training(
            event: new Event(
                startDate: Timestamp::createFromString($command->start_date),
                endDate: Timestamp::createFromString($command->end_date),
                timezone: $command->timezone,
                location: $command->location ? new Location($command->location) : null,
                active: $command->active,
                cancelled: $command->cancelled
            ),
            text: $contents,
            traceableTime: new TraceableTime(),
            definition: $definition,
            coaches: $trainingCoaches,
            teams: $teams,
            remark: $command->remark,
        ));
    }
}
