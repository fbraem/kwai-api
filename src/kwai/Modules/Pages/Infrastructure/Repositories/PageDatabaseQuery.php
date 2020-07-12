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
        ;
        $this->query->orderBy(Tables::PAGES()->priority, 'DESC');
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
    public function filterApplication(int $id): void
    {
        $this->query->andWhere(group(
            field(Tables::APPLICATIONS()->id)->eq($id)
        ));
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
    public function execute(?int $limit = null, ?int $offset = null)
    {
        $rows = parent::execute($limit, $offset);
        if (count($rows) == 0) {
            return [];
        }

        // Get all ids of the pages
        $idAlias = Tables::PAGES()->getAlias('id');
        $ids = array_map(fn($row) => (int) $row->{$idAlias}, $rows);

        // Get all content for these pages
        $contentQuery = new ContentDatabaseQuery($this->db);
        $contentQuery->filterIds($ids);
        if ($this->user) {
            $contentQuery->filterUser($this->user);
        }
        $contents = $contentQuery->execute();

        $pageColumnFilter = Tables::PAGES()->createColumnFilter();
        $applicationColumnFilter = Tables::APPLICATIONS()->createColumnFilter();
        $pages = [];
        foreach ($rows as $row) {
            $page = $pageColumnFilter->filter($row);
            $page->application = $applicationColumnFilter->filter($row);
            // Skip stories without content
            if (isset($contents[(string) $story->id])) {
                $page->contents = $contents[(string) $page->id];
                $pages[$page->id] = PageMapper::toDomain(
                    $page
                );
            }
        }

        return $pages;
    }

    protected function getColumns(): array
    {
        $aliasFn = Tables::PAGES()->getAliasFn();
        return [
            $aliasFn('id'),
            $aliasFn('enabled'),
            $aliasFn('remark'),
            $aliasFn('priority'),
            $aliasFn('created_at'),
            $aliasFn('updated_at'),
            Tables::APPLICATIONS()->getAliasFn()('id'),
            Tables::APPLICATIONS()->getAliasFn()('title')
        ];
    }
}
