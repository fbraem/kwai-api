<?php
/**
 * @package Pages
 * @subpackage Infrastructure
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Infrastructure\Repositories;

use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\Pages\Infrastructure\Tables;
use Kwai\Modules\Pages\Repositories\ContentQuery;
use function Latitude\QueryBuilder\alias;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\on;

/**
 * Class ContentDatabaseQuery
 */
class ContentDatabaseQuery extends DatabaseQuery implements ContentQuery
{
    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query
            ->from((string) Tables::CONTENTS())
            ->leftJoin(
                (string) Tables::AUTHORS(),
                on(
                    Tables::CONTENTS()->user_id,
                    Tables::AUTHORS()->id
                )
            )
        ;
    }

    /**
     * @inheritDoc
     */
    public function filterIds(array $ids): void
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->where(
            field(Tables::CONTENTS()->page_id)->in(...$ids)
        );
    }

    /**
     * @inheritDoc
     * @return object[]
     */
    public function execute(?int $limit = null, ?int $offset = null): array
    {
        $rows = parent::execute($limit, $offset);
        $contentFilter = Tables::CONTENTS()->createColumnFilter();
        $authorFilter = Tables::AUTHORS()->createColumnFilter();

        $result = [];
        foreach ($rows as $row) {
            $content = $contentFilter->filter($row);
            $content->author = $authorFilter->filter($row);
            if (! isset($result[$row->id])) {
                $result[$row->id] = [];
            }
            $result[$row->id][] = $content;
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        $aliasFn = Tables::CONTENTS()->getAliasFn();
        $aliasAuthorFn = Tables::AUTHORS()->getAliasFn();

        /** @noinspection PhpUndefinedFieldInspection */
        return [
            alias(Tables::CONTENTS()->page_id, 'id'),
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
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterUser(int $id): void
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(field(Tables::CONTENTS()->user_id)->eq($id));
    }
}
