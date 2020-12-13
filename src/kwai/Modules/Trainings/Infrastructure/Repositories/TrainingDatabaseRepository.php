<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Exceptions\TrainingNotFoundException as TrainingNotFoundExceptionAlias;
use Kwai\Modules\Trainings\Domain\Training;
use Kwai\Modules\Trainings\Domain\ValueObjects\TrainingCoach;
use Kwai\Modules\Trainings\Infrastructure\Mappers\TrainingMapper;
use Kwai\Modules\Trainings\Infrastructure\Tables;
use Kwai\Modules\Trainings\Repositories\TrainingQuery;
use Kwai\Modules\Trainings\Repositories\TrainingRepository;

/**
 * Class TrainingDatabaseRepository
 *
 * Repository for the Training entity.
 */
class TrainingDatabaseRepository extends DatabaseRepository implements TrainingRepository
{
    /**
     * @inheritDoc
     */
    public function getById(int $id): Entity
    {
        $query = $this->createQuery();
        $query->filterId($id);

        try {
            $entities = $this->getAll($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        if ($entities->count() === 1) {
            return $entities[$id];
        }

        throw new TrainingNotFoundExceptionAlias($id);
    }

    /**
     * @inheritDoc
     */
    public function createQuery(): TrainingQuery
    {
        return new TrainingDatabaseQuery($this->db);
    }

    /**
     * @inheritDoc
     */
    public function getAll(
        TrainingQuery $query,
        ?int $limit = null,
        ?int $offset = null
    ): Collection {
        /* @var Collection $trainings */
        $trainings = $query->execute($limit, $offset);
        return $trainings->mapWithKeys(
            fn($item, $key) => [ $key => TrainingMapper::toDomain($item) ]
        );
    }

    /**
     * @inheritDoc
     */
    public function create(Training $training): Entity
    {
        /* @var Collection $trainingData */
        /* @var Collection $contentData */
        /* @var Collection $coachesData */
        /* @var Collection $teamsData */

        [ $trainingData, $contentData, $coachesData, $teamsData]
            = TrainingMapper::toPersistence($training);

        // Insert training
        $query = $this->db->createQueryFactory()
            ->insert((string) Tables::TRAININGS())
            ->columns(...$trainingData->keys())
            ->values(... $trainingData->values())
        ;

        try {
            $this->db->execute($query);
        } catch(QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        $trainingId = $this->db->lastInsertId();

        // Insert all event contents
        $contentData->map(
            fn ($item) => $item->put('training_id', $trainingId)
        );
        $query = $this->db->createQueryFactory()
            ->insert((string) Tables::TRAINING_CONTENTS())
            ->columns(...$contentData->first()->keys())
        ;
        $contentData->each(
            fn(Collection $text) => $query->values(...$text->values())
        );

        try {
            $this->db->execute($query);
        } catch(QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        // Insert all coaches
        if ($coachesData->count() > 0) {
            $coachesData->map(
                fn ($item) => $item->put('training_id', $trainingId)
            );
            $query = $this->db->createQueryFactory()
                ->insert((string) Tables::TRAINING_COACHES())
                ->columns(...$coachesData->first()->keys())
            ;
            $coachesData->each(
                fn(Collection $trainingCoach) => $query->values(...$trainingCoach->values())
            );
            try {
                $this->db->execute($query);
            } catch(QueryException $e) {
                throw new RepositoryException(__METHOD__, $e);
            }
        }


        // Insert all teams
        if ($teamsData->count() > 0) {
            $teamsData->map(
                fn ($item) => $item->put('training_id', $trainingId)
            );
            $query = $this->db->createQueryFactory()
                ->insert((string) Tables::TRAINING_TEAMS())
                ->columns(...$teamsData->first()->keys())
            ;
            $teamsData->each(
                fn(Collection $team) => $query->values(...$team->values())
            );
            try {
                $this->db->execute($query);
            } catch(QueryException $e) {
                throw new RepositoryException(__METHOD__, $e);
            }
        }
        return new Entity($trainingId, $training);
    }
}
