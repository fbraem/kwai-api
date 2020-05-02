<?php
/**
 * @package Kwai
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Repositories\QueryException;
use Kwai\Modules\News\Domain\Exceptions\StoryNotFoundException;
use Kwai\Modules\News\Domain\Story;
use Kwai\Modules\News\Infrastructure\Mappers\StoryMapper;
use Kwai\Modules\News\Infrastructure\Tables;
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
        $query = $this->createQuery();
        $query->filterId($id);

        $entities = $query->execute();
        if (count($entities) == 1) {
            return $entities[$id];
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
            $rows = $this->db->execute($query->compile())->fetchAll();
        } catch (DatabaseException $e) {
            throw new QueryException($e->getMessage(), $e);
        }
        return $rows;
    }

    /**
     * @inheritDoc
     */
    public function create(Story $story): Entity
    {
        $data = StoryMapper::toPersistence($story);

        $query = $this->db->createQueryFactory()
            ->insert((string) Tables::STORIES())
            ->columns(
                ... array_keys($data)
            )
            ->values(
                ... array_values($data)
            )
            ->compile()
        ;

        try {
            $this->db->execute($query);
        } catch (DatabaseException $e) {
            throw new QueryException($query->sql(), $e);
        }

        $storyId = $this->db->lastInsertId();

        $query = $this->db->createQueryFactory()
            ->insert((string) Tables::CONTENTS())
            ->columns(
                'news_id',
                'locale',
                'format',
                'title',
                'content',
                'summary',
                'user_id'
            )
         ;
        foreach ($story->getContents() as $content) {
            $query->values(
                $storyId,
                (string) $content->getLocale(),
                (string) $content->getFormat(),
                $content->getTitle(),
                $content->getContent(),
                $content->getSummary(),
                $content->getAuthor()->id()
            );
        }
        $compiledQuery = $query->compile();
        try {
            $this->db->execute($compiledQuery);
        } catch (DatabaseException $e) {
            throw new QueryException($compiledQuery->sql(), $e);
        }

        return new Entity($storyId, $story);
    }
}
