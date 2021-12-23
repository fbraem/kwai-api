<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\UseCases;

use Kwai\Core\Domain\ValueObjects\Date;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Exceptions\CoachNotFoundException;
use Kwai\Modules\Trainings\Domain\Exceptions\DefinitionNotFoundException;
use Kwai\Modules\Trainings\Repositories\CoachRepository;
use Kwai\Modules\Trainings\Repositories\DefinitionRepository;
use Kwai\Modules\Trainings\Repositories\TrainingRepository;

/**
 * Class BrowseTrainings
 *
 * Use case for browsing trainings
 */
class BrowseTrainings
{
    /**
     * BrowseTrainings constructor.
     *
     * @param TrainingRepository   $repo
     * @param CoachRepository      $coachRepo
     * @param DefinitionRepository $defRepo
     */
    public function __construct(
        private TrainingRepository $repo,
        private CoachRepository $coachRepo,
        private DefinitionRepository $defRepo,
    ) {
    }

    /**
     * Factory method for this use case.
     *
     * @param TrainingRepository   $repo
     * @param CoachRepository      $coachRepo
     * @param DefinitionRepository $defRepo
     * @return BrowseTrainings
     */
    public static function create(
        TrainingRepository $repo,
        CoachRepository $coachRepo,
        DefinitionRepository $defRepo
    ) {
        return new self($repo, $coachRepo, $defRepo);
    }

    /**
     * Browse trainings
     *
     * @param BrowseTrainingsCommand $command
     * @return array
     * @throws QueryException
     * @throws RepositoryException
     * @throws CoachNotFoundException
     * @throws DefinitionNotFoundException
     */
    public function __invoke(BrowseTrainingsCommand $command)
    {
        $query = $this->repo->createQuery();

        if (isset($command->year)) {
            $query->filterYearMonth($command->year, $command->month);
        }

        if (isset($command->week)) {
            $query->filterWeek($command->week);
        }

        if (isset($command->start) || isset($command->end)) {
            $start = $command->start ? Date::createFromString($command->start) : Date::createToDay();
            $end = $command->end ? Date::createFromString($command->end) : Date::createFuture(7);
            $query->filterBetweenDates($start, $end);
        }

        if ($command->active) {
            $query->filterActive();
        }

        if ($command->coach) {
            $coaches = $this->coachRepo->getById($command->coach);
            if ($coaches->count() == 0) {
                throw new CoachNotFoundException($command->coach);
            }
            $query->filterCoach($coaches->first());
        }

        if ($command->definition) {
            $definition = $this->defRepo->getById($command->definition);
            $query->filterDefinition($definition);
        }

        $query->orderByDate();

        $count = $query->count();
        $trainings = $this->repo->getAll($query, $command->limit, $command->offset);

        return [
            $count, $trainings
        ];
    }
}
