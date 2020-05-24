<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Applications\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\Applications\Domain\Application;
use Kwai\Modules\Applications\Infrastructure\Mappers\ApplicationMapper;
use Kwai\Modules\Applications\Infrastructure\Tables;
use Kwai\Modules\Applications\Repositories\ApplicationQuery;
use function Latitude\QueryBuilder\field;

/**
 * Class ApplicationDatabaseQuery
 */
class ApplicationDatabaseQuery extends DatabaseQuery implements ApplicationQuery
{

    public function filterId(int $id): void
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::APPLICATIONS()->id)->eq($id)
        );
    }

    public function filterApplication(string $application)
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::APPLICATIONS()->app)->eq($application)
        );
    }

    protected function initQuery(): void
    {
        $this->query
            ->from((string) Tables::APPLICATIONS())
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
            $aliasFn('app'),
            $aliasFn('news'),
            $aliasFn('pages'),
            $aliasFn('events')
        ];
    }

    /**
     * @inheritDoc
     * @return Entity<Application>[]
     */
    public function execute(?int $limit = null, ?int $offset = null): array
    {
        $rows = parent::execute($limit, $offset);
        if (count($rows) == 0) {
            return [];
        }

        $applicationColumnFilter = Tables::APPLICATIONS()->createColumnFilter();
        $idAlias = Tables::APPLICATIONS()->getAlias('id');
        $applications = [];
        foreach ($rows as $row) {
            $applications[$row->{$idAlias}] = ApplicationMapper::toDomain(
                $applicationColumnFilter->filter($row)
            );
        }
        return $applications;
    }
}
