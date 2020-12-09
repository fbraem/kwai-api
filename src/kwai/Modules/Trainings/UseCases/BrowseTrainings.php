<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\UseCases;

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

        if ($command->active) {
            $query->filterActive();
        }

        if ($command->coach) {
            $coach = $this->coachRepo->getById($command->coach);
            $query->filterCoach($coach);
        }

        $count = $query->count();
        $trainings = $this->repo->getAll($query, $command->limit, $command->offset);

        return [
            $count, $trainings
        ];
    }
}
