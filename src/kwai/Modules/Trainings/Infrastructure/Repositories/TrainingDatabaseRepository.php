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
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Exceptions\TrainingNotFoundException;
use Kwai\Modules\Trainings\Domain\Training;
use Kwai\Modules\Trainings\Domain\ValueObjects\TrainingCoach;
use Kwai\Core\Infrastructure\Mappers\TextMapper;
use Kwai\Modules\Trainings\Infrastructure\Mappers\TrainingCoachMapper;
use Kwai\Modules\Trainings\Infrastructure\Mappers\TrainingMapper;
use Kwai\Modules\Trainings\Infrastructure\Tables;
use Kwai\Modules\Trainings\Repositories\TrainingQuery;
use Kwai\Modules\Trainings\Repositories\TrainingRepository;
use function Latitude\QueryBuilder\field;

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

        $entity = new Entity(
            $this->db->lastInsertId(),
            $training
        );
        // Insert all contents
        $this->insertContents($entity);

        // Insert all coaches
        $this->insertCoaches($entity);

        // Insert all teams
        $this->insertTeams($entity);

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function update(Entity $training): void
    {
        $data = TrainingMapper::toPersistence($training->domain());

        $queryFactory = $this->db->createQueryFactory();

        try {
            $this->db->begin();
        } catch(DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        // Update training
        try {
            $this->db->execute(
                $queryFactory
                    ->update((string) Tables::TRAININGS())
                    ->set($data->toArray())
                    ->where(field('id')->eq($training->id()))
            );
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        // Update contents
        // First delete all contents
        try {
            $this->db->execute(
                $queryFactory
                    ->delete((string) Tables::TRAINING_CONTENTS())
                    ->where(
                        field('training_id')->eq($training->id())
                    )
            );
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        // Next insert contents
        $this->insertContents($training);

        try {
            $this->db->commit();
        } catch(DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        // Update coaches
        // First delete all coaches
        try {
            $this->db->execute(
                $queryFactory
                    ->delete((string) Tables::TRAINING_COACHES())
                    ->where(
                        field('training_id')->eq($training->id())
                    )
            );
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        // Next insert coaches
        $this->insertCoaches($training);

        // Update teams
        // First delete all teams
        try {
            $this->db->execute(
                $queryFactory
                    ->delete((string) Tables::TRAINING_TEAMS())
                    ->where(
                        field('training_id')->eq($training->id())
                    )
            );
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        $this->insertTeams($training);
    }

    /**
     * Insert contents of the training
     *
     * @param Entity $training
     * @throws RepositoryException
     */
    private function insertContents(Entity $training)
    {
        /* @var Collection $contents */
        $contents = $training->getText();
        if ($contents->count() == 0) {
            return;
        }

        $contents
            ->transform(
                fn(Text $text) => TextMapper::toPersistence($text)
            )
            ->map(
                fn (Collection $item) => $item->put('training_id', $training->id())
            )
        ;

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
    }

    /**
     * Insert coaches of the training
     *
     * @param Entity $training
     * @throws RepositoryException
     */
    private function insertCoaches(Entity $training)
    {
        /* @var Collection $coaches */
        $coaches = $training->getCoaches();
        if ($coaches->count() == 0) {
            return;
        }

        $coaches
            ->transform(
            fn(TrainingCoach $coach) => TrainingCoachMapper::toPersistence($coach)
            )
            ->map(
            fn (Collection $item) => $item->put('training_id', $training->id())
            )
        ;

        $query = $this->db->createQueryFactory()
            ->insert((string) Tables::TRAINING_COACHES())
            ->columns(...$coaches->first()->keys())
        ;
        $coaches->each(
            fn(Collection $coach) => $query->values(...$coach->values())
        );
        try {
            $this->db->execute($query);
        } catch(QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }

    /**
     * Insert teams
     *
     * @param Entity $training
     * @throws RepositoryException
     */
    private function insertTeams(Entity $training)
    {
        /* @var Collection $teams */
        $teams = $training->getTeams();
        if ($teams->count() == 0) {
            return;
        }

        $teams
            ->transform(
                fn(Entity $team) => collect(['team_id' => $team->id()])
            )
            ->map(
                fn (Collection $item) => $item->put('training_id', $training->id())
            )
        ;

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
}
