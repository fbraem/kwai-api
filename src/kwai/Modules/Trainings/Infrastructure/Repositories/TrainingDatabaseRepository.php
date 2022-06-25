<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);


namespace Kwai\Modules\Trainings\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Trainings\Domain\Exceptions\TrainingNotFoundException;
use Kwai\Modules\Trainings\Domain\TeamEntity;
use Kwai\Modules\Trainings\Domain\Training;
use Kwai\Modules\Trainings\Domain\TrainingEntity;
use Kwai\Modules\Trainings\Domain\ValueObjects\TrainingCoach;
use Kwai\Modules\Trainings\Infrastructure\Mappers\TrainingCoachDTO;
use Kwai\Modules\Trainings\Infrastructure\Mappers\TrainingContentDTO;
use Kwai\Modules\Trainings\Infrastructure\Mappers\TrainingDTO;
use Kwai\Modules\Trainings\Infrastructure\TrainingCoachesTable;
use Kwai\Modules\Trainings\Infrastructure\TrainingContentsTable;
use Kwai\Modules\Trainings\Infrastructure\TrainingsTable;
use Kwai\Modules\Trainings\Infrastructure\TrainingTeamsTable;
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
     * TrainingDatabaseRepository constructor.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        parent::__construct(
            $db,
            static fn(TrainingDTO $dto) => $dto->createEntity()
        );
    }

    /**
     * @inheritDoc
     */
    public function getById(int $id): TrainingEntity
    {
        $query = $this->createQuery()->filterId($id);

        $entities = $this->getAll($query);
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
    public function create(Training $training): TrainingEntity
    {
        $dto = new TrainingDTO();
        $data = $dto->persist($training)
            ->training
            ->collect()
            ->forget('id')
        ;

        try {
            $this->db->begin();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        // Insert training
        $query = $this->db->createQueryFactory()
            ->insert(TrainingsTable::name())
            ->columns(...$data->keys())
            ->values(... $data->values())
        ;

        try {
            $this->db->execute($query);

            $entity = new TrainingEntity(
                $this->db->lastInsertId(),
                $training
            );
            // Insert all contents
            $this->insertContents($entity);

            // Insert all coaches
            $this->insertCoaches($entity);

            // Insert all teams
            $this->insertTeams($entity);
        } catch (QueryException $e) {
            try {
                $this->db->rollback();
            } catch (DatabaseException $e) {
                throw new RepositoryException(__METHOD__, $e);
            }
            throw new RepositoryException(__METHOD__, $e);
        }

        try {
            $this->db->commit();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function update(TrainingEntity $training): void
    {
        try {
            $this->db->begin();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        $data = (new TrainingDTO())
            ->persistEntity($training)
            ->training
            ->collect()
        ;
        $queryFactory = $this->db->createQueryFactory();

        // Update training
        try {
            $this->db->execute(
                $queryFactory
                    ->update(TrainingsTable::name())
                    ->set($data->toArray())
                    ->where(TrainingsTable::field('id')->eq($training->id()))
            );

            // Update contents
            // First delete all contents
            $this->db->execute(
                $queryFactory
                    ->delete(TrainingContentsTable::name())
                    ->where(
                        TrainingContentsTable::field('training_id')->eq($training->id())
                    )
            );

            // Next insert contents
            $this->insertContents($training);

            // Update coaches
            // First delete all coaches
            $this->db->execute(
                $queryFactory
                    ->delete(TrainingCoachesTable::name())
                    ->where(
                        TrainingCoachesTable::field('training_id')->eq($training->id())
                    )
            );

            // Next insert coaches
            $this->insertCoaches($training);

            // Update teams
            // First delete all teams
            $this->db->execute(
                $queryFactory
                    ->delete(TrainingTeamsTable::name())
                    ->where(
                        TrainingTeamsTable::field('training_id')->eq($training->id())
                    )
            );
            $this->insertTeams($training);
        } catch (QueryException $e) {
            try {
                $this->db->rollback();
            } catch (DatabaseException $e) {
                throw new RepositoryException(__METHOD__, $e);
            }
            throw new RepositoryException(__METHOD__, $e);
        }

        try {
            $this->db->commit();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }

    /**
     * Insert contents of the training
     *
     * @param TrainingEntity $training
     * @throws RepositoryException
     */
    private function insertContents(TrainingEntity $training)
    {
        $contents = $training->getText();
        if ($contents->count() == 0) {
            return;
        }

        $contents
            ->transform(
                static fn(Text $text)
                    => (new TrainingContentDTO())->persist($text)->content->collect()
            )
            ->map(
                static fn (Collection $item)
                    => $item->put('training_id', $training->id())
            )
        ;

        $query = $this->db->createQueryFactory()
            ->insert(TrainingContentsTable::name())
            ->columns(...$contents->first()->keys())
        ;
        $contents->each(
            fn(Collection $text) => $query->values(...$text->values())
        );
        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }

    /**
     * Insert coaches of the training
     *
     * @param TrainingEntity $training
     * @throws RepositoryException
     */
    private function insertCoaches(TrainingEntity $training)
    {
        $coaches = $training->getCoaches();
        if ($coaches->count() == 0) {
            return;
        }

        $coaches
            ->transform(
                static fn(TrainingCoach $coach)
                    => (new TrainingCoachDTO())->persist($coach)->trainingCoach->collect()
            )
            ->map(
                static fn (Collection $item) => $item->put('training_id', $training->id())
            )
        ;

        $query = $this->db->createQueryFactory()
            ->insert(TrainingCoachesTable::name())
            ->columns(...$coaches->first()->keys())
        ;
        $coaches->each(
            fn(Collection $coach) => $query->values(...$coach->values())
        );
        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }

    /**
     * Insert teams
     *
     * @param TrainingEntity $training
     * @throws RepositoryException
     */
    private function insertTeams(TrainingEntity $training)
    {
        $teams = $training->getTeams();
        if ($teams->count() == 0) {
            return;
        }

        $teams
            ->transform(
                fn(TeamEntity $team) => collect(['team_id' => $team->id()])
            )
            ->map(
                fn (Collection $item) => $item->put('training_id', $training->id())
            )
        ;

        $query = $this->db->createQueryFactory()
            ->insert(TrainingTeamsTable::name())
            ->columns(...$teams->first()->keys())
        ;
        $teams->each(
            fn(Collection $team) => $query->values(...$team->values())
        );
        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }
}
