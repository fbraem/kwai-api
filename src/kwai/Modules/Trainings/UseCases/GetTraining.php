<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\UseCases;

use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Exceptions\TrainingNotFoundException;
use Kwai\Modules\Trainings\Domain\TrainingEntity;
use Kwai\Modules\Trainings\Repositories\TrainingRepository;

/**
 * Class GetTraining
 *
 * Use case for getting a training with the given id.
 */
final class GetTraining
{
    /**
     * GetTraining constructor.
     *
     * @param TrainingRepository $repo
     */
    public function __construct(
        private readonly TrainingRepository $repo)
    {
    }

    /**
     * Factory method to create this use case.
     *
     * @param TrainingRepository $repo
     * @return GetTraining
     */
    public static function create(TrainingRepository $repo): GetTraining
    {
        return new self($repo);
    }

    /**
     * Get a training
     *
     * @param GetTrainingCommand $command
     * @return TrainingEntity
     * @throws RepositoryException
     * @throws TrainingNotFoundException
     */
    public function __invoke(GetTrainingCommand $command): TrainingEntity
    {
        $query = $this->repo->createQuery()->filterId($command->id);
        if ($command->withPresences) {
            $query->withPresences();
        }
        $trainings = $this->repo->getAll($query, 1);
        if ($trainings->count() === 0) {
            throw new TrainingNotFoundException($command->id);
        }
        return $trainings->first();
    }
}
