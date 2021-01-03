<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
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
     * StoryDatabaseQuery constructor.
     *
     * @param Connection $db
     */
    public function __construct(Connection $db)
    {
        parent::__construct(
            $db,
            Tables::STORIES()->getColumn('id')
        );
    }

    /**
     * @inheritDoc
     * @noinspection PhpUndefinedFieldInspection
     */
    protected function initQuery(): void
    {
        $this->query
            ->from((string) Tables::STORIES())
            ->join(
                (string) Tables::APPLICATIONS(),
                on(Tables::APPLICATIONS()->id, Tables::STORIES()->application_id)
            )
            ->join(
                (string) Tables::CONTENTS(),
                on(Tables::CONTENTS()->news_id, Tables::STORIES()->id)
            )
            ->leftJoin(
                (string) Tables::AUTHORS(),
                on(Tables::AUTHORS()->id, Tables::CONTENTS()->user_id)
            )
        ;
        $this->query->orderBy(Tables::STORIES()->publish_date, 'DESC');
    }

    /**
     * @inheritDoc
     */
    public function filterId(int $id): self
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::STORIES()->id)->eq($id)
        );
        return $this;
    }

    /**
     * @inheritDoc
     * @noinspection PhpUndefinedFieldInspection
     */
    public function filterPublishDate(int $year, ?int $month = null): self
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
        return $this;
    }

    /**
     * @inheritDoc
     * @noinspection PhpUndefinedFieldInspection
     */
    public function filterPromoted(): self
    {
        $now = Timestamp::createNow();
        $criteria = field(Tables::STORIES()->promotion)->gt(0)
            ->and(group(
                field(Tables::STORIES()->promotion_end_date)->isNull()
                    ->or(field(Tables::STORIES()->promotion_end_date)->gt((string) $now))
            ));
        $this->query->andWhere(group($criteria));
        $this->query->orderBy(Tables::STORIES()->promotion, 'DESC');
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $stories = collect();

        $filters = collect([
            Tables::STORIES()->getAliasPrefix(),
            Tables::APPLICATIONS()->getAliasPrefix(),
            Tables::CONTENTS()->getAliasPrefix(),
            Tables::AUTHORS()->getAliasPrefix()
        ]);

        foreach ($rows as $row) {
            [ $story, $application, $content, $user ] = $row->filterColumns($filters);
            $story->put('application', $application);
            if (!$stories->has($story->get('id'))) {
                $stories->put($story['id'], $story);
                $story->put('contents', new Collection());
            }
            $content->put('creator', $user);
            $stories[$story['id']]['contents']->push($content);
        }

        return $stories;
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        $aliasFn = Tables::STORIES()->getAliasFn();
        $aliasApplicationFn = Tables::APPLICATIONS()->getAliasFn();
        $aliasContentFn = Tables::CONTENTS()->getAliasFn();
        $aliasAuthorFn = Tables::AUTHORS()->getAliasFn();
        return [
            $aliasFn('id'),
            $aliasFn('enabled'),
            $aliasFn('promotion'),
            $aliasFn('promotion_end_date'),
            $aliasFn('publish_date'),
            $aliasFn('timezone'),
            $aliasFn('end_date'),
            $aliasFn('remark'),
            $aliasFn('created_at'),
            $aliasFn('updated_at'),
            $aliasApplicationFn('id'),
            $aliasApplicationFn('title'),
            $aliasApplicationFn('name'),
            $aliasContentFn('locale'),
            $aliasContentFn('format'),
            $aliasContentFn('title'),
            $aliasContentFn('content'),
            $aliasContentFn('summary'),
            $aliasContentFn('created_at'),
            $aliasContentFn('updated_at'),
            $aliasAuthorFn('id'),
            $aliasAuthorFn('first_name'),
            $aliasAuthorFn('last_name')
        ];
    }

    /**
     * @inheritDoc
     * @noinspection PhpUndefinedFieldInspection
     */
    public function filterApplication(int|string $appNameOrId): self
    {
        if (is_string($appNameOrId)) {
            $this->query->andWhere(group(
                field(Tables::APPLICATIONS()->name)->eq($appNameOrId)
            ));
        } else {
            $this->query->andWhere(group(
                field(Tables::APPLICATIONS()->id)->eq($appNameOrId)
            ));
        }
        return $this;
    }

    /**
     * @inheritDoc
     * @noinspection PhpUndefinedFieldInspection
     */
    public function filterVisible(): self
    {
        $now = Timestamp::createNow();
        $this->query->andWhere(group(
            field(Tables::STORIES()->enabled)->eq(true)
            ->and(field(Tables::STORIES()->publish_date)->lte((string) $now))
            ->or(group(
                field(Tables::STORIES()->end_date)->isNotNull()
                ->and(field(Tables::STORIES()->end_date)->gt((string) $now))
            ))
        ));
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterUser(int $id): self
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(group(
            field(Tables::CONTENTS()->user_id)->eq($id)
        ));
        return $this;
    }
}
