<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\DatabaseException;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\News\Domain\Exceptions\StoryNotFoundException;
use Kwai\Modules\News\Infrastructure\Mappers\StoryMapper;
use Kwai\Modules\News\Infrastructure\Tables;
use Kwai\Modules\News\Repositories\StoryRepository;
use Latitude\QueryBuilder\Query\SelectQuery;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\on;

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
        /** @noinspection PhpUndefinedFieldInspection */
        $query = $this->createBaseQuery()
            ->where(field(Tables::STORIES()->id)->eq($id))
            ->compile()
        ;

        try {
            $story = $this->db->execute($query)->fetch();
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        if ($story) {
            $alias = Tables::STORIES()->getAlias('contents');
            $story->{$alias} = $this->getContents([$id]);
            return StoryMapper::toDomain(
                Tables::STORIES()->createColumnFilter()->filter($story)
            );
        }
        throw new StoryNotFoundException($id);
    }

    /**
     * Create base SELECT query
     *
     * @return SelectQuery
     */
    private function createBaseQuery(): SelectQuery
    {
        $aliasFn = Tables::STORIES()->getAliasFn();
        return $this->db->createQueryFactory()
            ->select(
                $aliasFn('id'),
                $aliasFn('enabled'),
                $aliasFn('promoted'),
                $aliasFn('promoted_end_date'),
                $aliasFn('publish_date'),
                $aliasFn('timezone'),
                $aliasFn('end_date'),
                $aliasFn('remark'),
                $aliasFn('created_at'),
                $aliasFn('updated_at')
            )
            ->from((string) Tables::STORIES())
        ;
    }

    /**
     * @param int[] $stories
     * @return array
     * @throws RepositoryException
     */
    private function getContents(array $stories): array
    {
        $aliasFn = Tables::CONTENTS()->getAliasFn();
        $aliasAuthorFn = Tables::AUTHORS()->getAliasFn();

        /** @noinspection PhpUndefinedFieldInspection */
        $query = $this->db->createQueryFactory()
            ->select(
                $aliasFn('news_id'),
                $aliasFn('locale'),
                $aliasFn('format'),
                $aliasFn('title'),
                $aliasFn('content'),
                $aliasFn('summary'),
                $aliasFn('user_id'),
                $aliasFn('created_at'),
                $aliasFn('updated_at'),
                $aliasAuthorFn('id'),
                $aliasAuthorFn('first_name'),
                $aliasAuthorFn('last_name')
            )
            ->from((string) Tables::CONTENTS())
            ->join(
                (string) Tables::AUTHORS(),
                on(
                    Tables::CONTENTS()->user_id,
                    Tables::AUTHORS()->id
                )
            )
            ->where(field(Tables::CONTENTS()->news_id)->in($stories))
            ->compile()
        ;

        try {
            $stmt = $this->db->execute($query);
        } catch (DatabaseException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        $rows = $stmt->fetchAll();

        $contentFilter = Tables::CONTENTS()->createColumnFilter();
        $authorFilter = Tables::AUTHORS()->createColumnFilter();

        $result = [];
        foreach ($rows as $row) {
            $content = $contentFilter->filter($row);
            $content->author = $authorFilter->filter($row);
            if (! isset($result[$row->news_id])) {
                $result[$row->news_id] = [];
            }
            $result[$row->news_id][] = $content;
        }

        return $result;
    }
}
