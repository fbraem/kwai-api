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
use Kwai\Modules\Pages\Repositories\PageQuery;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\group;
use function Latitude\QueryBuilder\on;

/**
 * Class PageDatabaseQuery
 *
 * Class for building a query to select pages.
 */
class PageDatabaseQuery extends DatabaseQuery implements PageQuery
{
    protected function initQuery(): void
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query
            ->from((string) Tables::PAGES())
            ->join(
                (string) Tables::APPLICATIONS(),
                on(Tables::APPLICATIONS()->id, Tables::PAGES()->application_id)
            )
            ->join(
                (string) Tables::CONTENTS(),
                on(Tables::CONTENTS()->page_id, Tables::PAGES()->id)
            )
            ->join(
                (string) Tables::AUTHORS(),
                on(Tables::AUTHORS()->id, Tables::CONTENTS()->user_id)
            )
        ;
    }

    /**
     * @inheritDoc
     */
    public function filterId(int $id): self
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::PAGES()->id)->eq($id)
        );
        return $this;
    }

    /**
     * @inheritDoc
     * @noinspection PhpUndefinedFieldInspection
     */
    public function filterApplication($nameOrId): self
    {
        if (is_string($nameOrId)) {
            $this->query->andWhere(group(
                field(Tables::APPLICATIONS()->name)->eq($nameOrId)
            ));
        } else {
            $this->query->andWhere(group(
                field(Tables::APPLICATIONS()->id)->eq($nameOrId)
            ));
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function filterVisible(): self
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(group(
            field(Tables::PAGES()->enabled)->eq(true)
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

    /**
     * @inheritDoc
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        // TODO: For now the relation page -> content is 1 on 1. In the future
        // TODO: it will be possible to translate a page, which will result in
        // TODO: 1 on many.
        $rows = parent::walk($limit, $offset);

        $prefixes = [
            Tables::PAGES()->getAliasPrefix(),
            Tables::APPLICATIONS()->getAliasPrefix(),
            Tables::CONTENTS()->getAliasPrefix(),
            Tables::AUTHORS()->getAliasPrefix()
        ];

        $pages = new Collection();

        foreach ($rows as $row) {
            [ $page, $application, $content, $author ] =
                $row->filterColumns($prefixes);
            $page->put('application', $application);
            $content->put('creator', $author);
            $page->put('contents', new Collection([$content]));
            $pages->put($page->get('id'), $page);
        }

        return $pages;
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        $aliasFn = Tables::PAGES()->getAliasFn();
        $aliasContentFn = Tables::CONTENTS()->getAliasFn();
        $aliasAuthorFn = Tables::AUTHORS()->getAliasFn();
        return [
            $aliasFn('id'),
            $aliasFn('enabled'),
            $aliasFn('remark'),
            $aliasFn('priority'),
            $aliasFn('created_at'),
            $aliasFn('updated_at'),
            Tables::APPLICATIONS()->getAliasFn()('id'),
            Tables::APPLICATIONS()->getAliasFn()('title'),
            Tables::APPLICATIONS()->getAliasFn()('name'),
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
     */
    public function orderByPriority(): self
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->orderBy(Tables::PAGES()->priority, 'DESC');
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function orderByApplication(): self
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->orderBy(Tables::APPLICATIONS()->title);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function orderByCreationDate(): self
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->orderBy(Tables::PAGES()->created_at, 'DESC');
        return $this;
    }
}
