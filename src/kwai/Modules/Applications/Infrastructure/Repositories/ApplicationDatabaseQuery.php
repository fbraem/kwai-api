<?php
/**
 * @package Modules
 * @subpackage Applications
 */
declare(strict_types=1);

namespace Kwai\Modules\Applications\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\Applications\Infrastructure\Tables;
use Kwai\Modules\Applications\Repositories\ApplicationQuery;
use function Latitude\QueryBuilder\field;

/**
 * Class ApplicationDatabaseQuery
 */
class ApplicationDatabaseQuery extends DatabaseQuery implements ApplicationQuery
{

    public function filterId(int $id): self
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::APPLICATIONS()->id)->eq($id)
        );
        return $this;
    }

    public function filterApplication(string $application): self
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::APPLICATIONS()->name)->eq($application)
        );
        return $this;
    }

    protected function initQuery(): void
    {
        $this->query
            ->from((string) Tables::APPLICATIONS())
            ->orderBy(Tables::APPLICATIONS()->getColumn('weight'), 'DESC')
        ;
    }

    protected function getColumns(): array
    {
        $aliasFn = Tables::APPLICATIONS()->getAliasFn();
        return [
            $aliasFn('id'),
            $aliasFn('title'),
            $aliasFn('description'),
            $aliasFn('remark'),
            $aliasFn('created_at'),
            $aliasFn('updated_at'),
            $aliasFn('short_description'),
            $aliasFn('weight'),
            $aliasFn('name'),
            $aliasFn('news'),
            $aliasFn('pages'),
            $aliasFn('events')
        ];
    }

    /**
     * @inheritDoc
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $applications = new Collection();

        $rows = parent::execute($limit, $offset);
        if ($rows->isNotEmpty()) {
            $filters = new Collection([
                Tables::APPLICATIONS()->getAliasPrefix()
            ]);

            foreach ($rows as $row) {
                [ $application ] = $row->filterColumns($filters);
                $applications->put($application->get('id'), $application);
            }
        }
        return $applications;
    }
}
