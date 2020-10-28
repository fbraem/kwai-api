<?php
/**
 * @package Pages
 * @subpackage Infrastructure
 * @noinspection PhpUndefinedFieldInspection
 */
declare(strict_types=1);

namespace Kwai\Modules\Pages\Infrastructure\Repositories;

use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\Pages\Infrastructure\Mappers\PageMapper;
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
    public function filterId(int $id): void
    {
        $this->query->andWhere(
            field(Tables::PAGES()->id)->eq($id)
        );
    }

    /**
     * @inheritDoc
     */
    public function filterApplication($nameOrId): void
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
    }

    /**
     * @inheritDoc
     */
    public function filterVisible(): void
    {
        $this->query->andWhere(group(
            field(Tables::PAGES()->enabled)->eq(true)
        ));
    }

    /**
     * @inheritDoc
     */
    public function filterUser(int $id): void
    {
        $this->query->andWhere(group(
            field(Tables::CONTENTS()->user_id)->eq($id)
        ));
    }

    /**
     * @inheritDoc
     */
    public function execute(?int $limit = null, ?int $offset = null)
    {
        // TODO: For now the relation page -> content is 1 on 1. In the future
        // TODO: it will be possible to translate a page, which will result in
        // TODO: 1 on many.
        $rows = parent::execute($limit, $offset);
        if (count($rows) == 0) {
            return [];
        }

        $pageColumnFilter = Tables::PAGES()->createColumnFilter();
        $applicationColumnFilter = Tables::APPLICATIONS()->createColumnFilter();
        $contentColumnFilter = Tables::CONTENTS()->createColumnFilter();
        $authorColumnFilter = Tables::AUTHORS()->createColumnFilter();

        $pages = [];
        foreach ($rows as $row) {
            $page = $pageColumnFilter->filter($row);
            $page->application = $applicationColumnFilter->filter($row);
            $page->contents = [
                $contentColumnFilter->filter($row)
            ];
            $page->contents[0]->author = $authorColumnFilter->filter($row);
            $pages[$page->id] = PageMapper::toDomain($page);
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
    public function orderByPriority(): void
    {
        $this->query->orderBy(Tables::PAGES()->priority, 'DESC');
    }

    /**
     * @inheritDoc
     */
    public function orderByApplication(): void
    {
        $this->query->orderBy(Tables::APPLICATIONS()->title);
    }

    /**
     * @inheritDoc
     */
    public function orderByCreationDate(): void
    {
        $this->query->orderBy(Tables::PAGES()->created_at, 'DESC');
    }
}
