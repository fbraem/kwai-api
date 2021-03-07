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
use Kwai\Modules\Trainings\Repositories\CoachRepository;
use Kwai\Modules\Trainings\Repositories\TrainingRepository;

/**
 * Class BrowseTrainings
 *
 * Use case for browsing trainings
 */
class BrowseTrainings
{
    private TrainingRepository $repo;

    private CoachRepository $coachRepo;

    /**
     * BrowseTrainings constructor.
     *
     * @param TrainingRepository $repo
     * @param CoachRepository    $coachRepo
     */
    public function __construct(TrainingRepository $repo, CoachRepository $coachRepo)
    {
        $this->repo = $repo;
        $this->coachRepo = $coachRepo;
    }

    public static function create(TrainingRepository $repo, CoachRepository $coachRepo)
    {
        return new self($repo, $coachRepo);
    }

    /**
     * Browse trainings
     *
     * @param BrowseTrainingsCommand $command
     * @return array
     * @throws QueryException
     * @throws RepositoryException
     * @throws CoachNotFoundException
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

        $count = $query->count();
        $trainings = $this->repo->getAll($query, $command->limit, $command->offset);

        return [
            $count, $trainings
        ];
    }
}
