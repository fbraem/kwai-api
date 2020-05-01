<?php
/**
 * @package Kwai
 * @subpackage News
 * @noinspection PhpUndefinedFieldInspection
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\News\Domain\Story;
use Kwai\Modules\News\Infrastructure\Mappers\StoryMapper;
use Kwai\Modules\News\Infrastructure\Tables;
use Kwai\Modules\News\Repositories\StoryQuery;
use function Latitude\QueryBuilder\criteria;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\func;
use function Latitude\QueryBuilder\group;
use function Latitude\QueryBuilder\literal;
use function Latitude\QueryBuilder\on;

/**
 * Class StoryQuery
 *
 * Class for building a query for selecting news stories.
 */
class StoryDatabaseQuery extends DatabaseQuery implements StoryQuery
{
    /**
     * Used to filter the contents on the user.
     */
    private ?int $user = null;

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query
            ->from((string) Tables::STORIES())
            ->join(
                (string) Tables::CATEGORIES(),
                on(Tables::CATEGORIES()->id, Tables::STORIES()->category_id)
            );
    }

    /**
     * @inheritDoc
     */
    public function filterId(int $id): void
    {
        $this->query->andWhere(
            field(Tables::STORIES()->id)->eq($id)
        );
    }

    /**
     * @inheritDoc
     */
    public function filterPublishDate(int $year, ?int $month = null): void
    {
        $criteria = criteria(
            "%s = %d",
            func(
                'YEAR',
                Tables::STORIES()->publish_date
            ),
            literal($year)
        );
        if ($month) {
            $criteria = $criteria->and(
                criteria(
                    "%s = %d",
                    func(
                        'MONTH',
                        Tables::STORIES()->publish_date
                    ),
                    literal($month)
                )
            );
        }
        $this->query->andWhere(group($criteria));
    }

    /**
     * @inheritDoc
     */
    public function filterPromoted(): void
    {
        $now = Timestamp::createNow();
        $criteria = field(Tables::STORIES()->promoted)->gt(0)
            ->and(group(
                field(Tables::STORIES()->promoted_end_date)->isNull()
                    ->or(field(Tables::STORIES()->promoted_end_date)->gt((string) $now))
            ));
        $this->query->andWhere(group($criteria));
    }

    /**
     * @inheritDoc
     * @return Entity<Story>[]
     */
    public function execute(?int $limit = null, ?int $offset = null): array
    {
        $rows = parent::execute($limit, $offset);
        if (count($rows) == 0) {
            return [];
        }

        // Get all ids of the stories
        $idAlias = Tables::STORIES()->getAlias('id');
        $ids = array_map(fn($row) => (int) $row->{$idAlias}, $rows);

        // Get all content for these news stories
        $contentQuery = new ContentDatabaseQuery($this->db);
        $contentQuery->filterIds($ids);
        if ($this->user) {
            $contentQuery->filterUser($this->user);
        }
        $contents = $contentQuery->execute();

        $storyColumnFilter = Tables::STORIES()->createColumnFilter();
        $categoryColumnFilter = Tables::CATEGORIES()->createColumnFilter();
        $stories = [];
        foreach ($rows as $row) {
            $story = $storyColumnFilter->filter($row);
            $story->category = $categoryColumnFilter->filter($row);
            // Skip stories without content
            if (isset($contents[(string) $story->id])) {
                $story->contents = $contents[(string)$story->id];
                $stories[$story->id] = StoryMapper::toDomain(
                    $story
                );
            }
        }

        return $stories;
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        $aliasFn = Tables::STORIES()->getAliasFn();
        return [
            $aliasFn('id'),
            $aliasFn('enabled'),
            $aliasFn('promoted'),
            $aliasFn('promoted_end_date'),
            $aliasFn('publish_date'),
            $aliasFn('timezone'),
            $aliasFn('end_date'),
            $aliasFn('remark'),
            $aliasFn('created_at'),
            $aliasFn('updated_at'),
            Tables::CATEGORIES()->getAliasFn()('id'),
            Tables::CATEGORIES()->getAliasFn()('name')
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterCategory(int $id): void
    {
        $this->query->andWhere(group(
            field(Tables::CATEGORIES()->id)->eq($id)
        ));
    }

    /**
     * @inheritDoc
     */
    public function filterVisible(): void
    {
        $now = Timestamp::createNow();
        $this->query->andWhere(group(
            field(Tables::STORIES()->enabled)->eq(true)
            ->or(field(Tables::STORIES()->publish_date)->lte((string) $now))
            ->or(group(
                field(Tables::STORIES()->end_date)->isNotNull()
                ->and(field(Tables::STORIES()->end_date)->gt((string) $now))
            ))
        ));
    }

    /**
     * @inheritDoc
     */
    public function filterUser(int $id): void
    {
        $this->user = $id;
    }
}