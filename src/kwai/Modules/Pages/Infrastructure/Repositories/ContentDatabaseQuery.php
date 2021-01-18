<?php
/**
 * @package Modules
 * @subpackage Pages
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Infrastructure\Repositories;

use Illuminate\Support\Collection;
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
    public function filterIds(int ...$ids): self
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->where(
            field(Tables::CONTENTS()->page_id)->in(...$ids)
        );
        return $this;
    }

    /**
     * @inheritDoc
     * @return object[]
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $prefixes = [
            Tables::CONTENTS()->getAliasPrefix(),
            Tables::AUTHORS()->getAliasPrefix()
        ];

        $rows = parent::walk($limit, $offset);

        $result = new Collection();
        foreach ($rows as $row) {
            [ $content, $author ] = $row->filterColumns($prefixes);
            $content->put('author', $author);
            if (!$result->has($content->get('id'))) {
                $result->put($content->get('id'), new Collection());
            }
            $result->get($row->id)->push($content);
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
    public function filterUser(int $id): self
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(field(Tables::CONTENTS()->user_id)->eq($id));
        return $this;
    }
}
