<?php
/**
 * @package Modules
 * @subpackage News
 */
declare(strict_types=1);

namespace Kwai\Modules\News\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\News\Infrastructure\Tables;
use Kwai\Modules\News\Repositories\ApplicationQuery;
use function Latitude\QueryBuilder\field;

/**
 * Class ApplicationDatabaseQuery
 */
class ApplicationDatabaseQuery extends DatabaseQuery implements ApplicationQuery
{
    /**
     * @inheritDoc
     */
    public function filterId(int $id): ApplicationQuery
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->where(field(Tables::APPLICATIONS()->id)->eq($id));
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query->from((string) Tables::APPLICATIONS());
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        $aliasFn = Tables::APPLICATIONS()->getAliasFn();

        return [
            $aliasFn('id'),
            $aliasFn('title'),
            $aliasFn('name')
        ];
    }

    /**
     * @inheritDoc
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $applications = new Collection();
        foreach ($rows as $row) {
            [ $application ] = $row->filterColumns([
                Tables::APPLICATIONS()->getAliasPrefix()
            ]);
            $applications->put($application->get('id'), $application);
        }

        return $applications;
    }
}
