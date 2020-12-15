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
use Kwai\Modules\Trainings\Domain\Exceptions\DefinitionNotFoundException;
use Kwai\Modules\Trainings\Domain\Training;
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
    public function __construct(
        private TrainingRepository $trainingRepo,
        private DefinitionRepository $definitionRepository,
        private TeamRepository $teamRepository,
        private CoachRepository $coachRepository
    ) {
    }

    /**
     * @param CreateTrainingCommand $command
     * @param Creator               $creator
     * @return Entity<Training>
     * @throws RepositoryException
     * @throws DefinitionNotFoundException
     * @throws QueryException
     */
    public function __invoke(CreateTrainingCommand $command, Creator $creator): Entity
    {
        $definition = isset($command->definition_id) ?
            $this->definitionRepository->getById($command->definition_id) : null;
        $teams = $this->teamRepository->getById(...$command->teams);
        $coaches = $this->coachRepository->getById(... $command->coaches);

        $contents = new Collection();
        foreach($command->contents as $text) {
            $contents->add(new Text(
                new Locale($text->locale),
                new DocumentFormat($text->format),
                $text->title,
                $text->summary,
                $text->content,
                $creator
            ));
        }

        return $this->trainingRepo->create(new Training(
            event: new Event(
                startDate: Timestamp::createFromString($command->start_date),
                endDate: Timestamp::createFromString($command->end_date),
                location: new Location($command->location),
                active: $command->active,
                cancelled: $command->cancelled
            ),
            text: $contents,
            traceableTime: new TraceableTime(),
            definition: $definition,
            coaches: $coaches,
            teams: $teams,
            remark: $command->remark,
        ));
    }
}
