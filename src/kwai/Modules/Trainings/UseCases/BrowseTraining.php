<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\UseCases;

use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Modules\Trainings\Repositories\TrainingRepository;

/**
 * Class BrowseTraining
 *
 * Use case for browsing trainings
 */
class BrowseTraining
{
    private TrainingRepository $repo;

    /**
     * BrowseTraining constructor.
     *
     * @param TrainingRepository $repo
     */
    public function __construct(TrainingRepository $repo)
    {
        $this->repo = $repo;
    }

    public static function create(TrainingRepository $repo)
    {
        return new self($repo);
    }

    /**
     * Browse trainings
     *
     * @param BrowseTrainingCommand $command
     * @return array
     * @throws QueryException
     */
    public function __invoke(BrowseTrainingCommand $command)
    {
        $query = $this->repo->createQuery();

        if (isset($command->year)) {
            $query->filterYearMonth($command->year, $command->month);
        }

        if ($command->active) {
            $query->filterActive();
        }

        $count = $query->count();
        $trainings = $query->execute($command->limit, $command->offset);

        return [
            $count, $trainings
        ];
    }
}
