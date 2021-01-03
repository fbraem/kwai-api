<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Text;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Mappers\TextMapper;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Domain\Exceptions\StoryNotFoundException;
use Kwai\Modules\News\Domain\Story;
use Kwai\Modules\News\Infrastructure\Mappers\StoryMapper;
use Kwai\Modules\News\Infrastructure\Tables;
use Kwai\Modules\News\Repositories\StoryQuery;
use Kwai\Modules\News\Repositories\StoryRepository;
use function Latitude\QueryBuilder\alias;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\func;

/**
 * Class StoryDatabaseRepository
 */
class StoryDatabaseRepository extends DatabaseRepository implements StoryRepository
{
    /**
     * @inheritDoc
     */
    public function getById(int $id): Entity
    {
        try {
            $entities = $this->getAll($this->createQuery()->filterId($id));
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        if ($entities->count() === 1) {
            return $entities->first();
        }
        throw new StoryNotFoundException($id);
    }

    /**
     * @inheritDoc
     */
    public function createQuery(): StoryDatabaseQuery
    {
        return new StoryDatabaseQuery($this->db);
    }

    /**
     * @inheritDoc
     * @noinspection PhpUndefinedFieldInspection
     */
    public function getArchive(): array
    {
        $query = $this->db->createQueryFactory()
            ->select(
                alias(func('YEAR', Tables::STORIES()->publish_date), 'year'),
                alias(func('MONTH', Tables::STORIES()->publish_date), 'month'),
                alias(func('COUNT', Tables::STORIES()->id), 'count')
            )->from((string) Tables::STORIES())
        ;
        $now = Timestamp::createNow();
        // Only count the published news stories
        $query->where(field(Tables::STORIES()->publish_date)->lt($now));
        // Don't count the disables news stories
        $query->andWhere(field(Tables::STORIES()->enabled)->eq(true));
        $query->groupBy('year', 'month');
        $query->orderBy('year', 'DESC');
        $query->orderBy('month', 'DESC');
        try {
            $rows = $this->db->execute($query)->fetchAll();
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        return $rows;
    }

    /**
     * @inheritDoc
     */
    public function create(Story $story): Entity
    {
        try {
            $this->db->begin();
        } catch(DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        $data = StoryMapper::toPersistence($story);
        $query = $this->db->createQueryFactory()
            ->insert((string) Tables::STORIES())
            ->columns(
                ... $data->keys()
            )
            ->values(
                ... $data->values()
            )
        ;

        try {
            $this->db->execute($query);
            $entity = new Entity(
                $this->db->lastInsertId(),
                $story
            );

            $this->insertContents($entity);
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
        } catch(DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        return $entity;
    }

    /**
     * @inheritDoc
     */
    public function update(Entity $story): void
    {
        try {
            $this->db->begin();
        } catch(DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        try {
            $data = StoryMapper::toPersistence($story->domain());
            $queryFactory = $this->db->createQueryFactory();

            $this->db->execute(
                $queryFactory
                    ->update((string)Tables::STORIES())
                    ->set($data->toArray())
                    ->where(field('id')->eq($story->id()))
            );

            // Update contents
            // First delete all contents
            $this->db->execute(
                $queryFactory
                    ->delete((string)Tables::CONTENTS())
                    ->where(
                        field('training_id')->eq($story->id())
                    )
            );

            // Next insert contents again
            $this->insertContents($story);
        } catch(QueryException $qe) {
            try {
                $this->db->rollback();
            } catch (DatabaseException $e) {
                throw new RepositoryException(__METHOD__, $e);
            }
            throw new RepositoryException(__METHOD__, $qe);
        }

        try {
            $this->db->commit();
        } catch(DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }

    /**
     * @inheritDoc
     */
    public function remove(Entity $story)
    {
        try {
            $this->db->begin();
        } catch(DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        $queryFactory = $this->db->createQueryFactory();
        try {
            $query = $queryFactory
                ->delete((string) Tables::CONTENTS())
                ->where(field('news_id')->eq($story->id()))
            ;

            try {
                $this->db->execute($query);
            } catch (QueryException $e) {
                throw new RepositoryException(__METHOD__, $e);
            }

            $query = $queryFactory
                ->delete((string) Tables::STORIES())
                ->where(field('id')->eq($story->id()))
            ;

            $this->db->execute($query);
        } catch (QueryException $qe) {
            try {
                $this->db->rollback();
            } catch (DatabaseException $e) {
                throw new RepositoryException(__METHOD__, $e);
            }
            throw new RepositoryException(__METHOD__, $qe);
        }

        try {
            $this->db->commit();
        } catch(DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }

    /**
     * @param Entity<Story> $story
     * @throws QueryException
     */
    private function insertContents(Entity $story): void
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $contents = $story->getContents();
        $contents
            ->transform(
                fn(Text $text) => TextMapper::toPersistence($text)
            )
            ->map(
                fn(Collection $item) => $item->put('news_id', $story->id())
            );

        $query = $this->db->createQueryFactory()
            ->insert((string)Tables::CONTENTS())
            ->columns(...$contents->first()->keys());
        $contents->each(
            fn(Collection $text) => $query->values(...$text->values())
        );

        $this->db->execute($query);
    }

    /**
     * @inheritDoc
     */
    public function getAll(
        ?StoryQuery $query = null,
        ?int $limit = null,
        ?int $offset = null): Collection
    {
        $query ??= $this->createQuery();

        /* @var Collection $stories */
        $stories = $query->execute($limit, $offset);
        return $stories->mapWithKeys(
            fn($item, $key) => [
                $key => new Entity((int) $key, StoryMapper::toDomain($item))
            ]
        );
    }
}
