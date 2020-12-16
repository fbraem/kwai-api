<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Exceptions\TrainingNotFoundException;
use Kwai\Modules\Trainings\Domain\Training;
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

        throw new TrainingNotFoundException($id);
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
            fn($item, $key) => [
                $key => new Entity((int) $key, TrainingMapper::toDomain($item))
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function create(Training $training): Entity
    {
        $data = TrainingMapper::toPersistence($training);

        $contents = $data->pull('contents', new Collection());
        $coaches = $data->pull('coaches', new Collection());
        $teams = $data->pull('teams', new Collection());

        // Insert training
        $query = $this->db->createQueryFactory()
            ->insert((string) Tables::TRAININGS())
            ->columns(...$data->keys())
            ->values(... $data->values())
        ;

        try {
            $this->db->execute($query);
        } catch(QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        $trainingId = $this->db->lastInsertId();

        // Insert all contents
        $contents->map(
            fn ($item) => $item->put('training_id', $trainingId)
        );
        $query = $this->db->createQueryFactory()
            ->insert((string) Tables::TRAINING_CONTENTS())
            ->columns(...$contents->first()->keys())
        ;
        $contents->each(
            fn(Collection $text) => $query->values(...$text->values())
        );

        try {
            $this->db->execute($query);
        } catch(QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        // Insert all coaches
        if ($coaches->count() > 0) {
            $coaches->map(
                fn ($item) => $item->put('training_id', $trainingId)
            );
            $query = $this->db->createQueryFactory()
                ->insert((string) Tables::TRAINING_COACHES())
                ->columns(...$coaches->first()->keys())
            ;
            $coaches->each(
                fn(Collection $trainingCoach) => $query->values(...$trainingCoach->values())
            );
            try {
                $this->db->execute($query);
            } catch(QueryException $e) {
                throw new RepositoryException(__METHOD__, $e);
            }
        }


        // Insert all teams
        if ($teams->count() > 0) {
            $teams->map(
                fn ($item) => $item->put('training_id', $trainingId)
            );
            $query = $this->db->createQueryFactory()
                ->insert((string) Tables::TRAINING_TEAMS())
                ->columns(...$teams->first()->keys())
            ;
            $teams->each(
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
