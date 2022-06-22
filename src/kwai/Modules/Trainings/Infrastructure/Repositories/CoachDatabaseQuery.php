<?php
/**
 * @package Modules
 * @subpackage Trainings
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\Trainings\Infrastructure\CoachesTable;
use Kwai\Modules\Trainings\Infrastructure\Mappers\CoachDTO;
use Kwai\Modules\Trainings\Infrastructure\MembersTable;
use Kwai\Modules\Trainings\Infrastructure\PersonsTable;
use Kwai\Modules\Trainings\Repositories\CoachQuery;
use function Latitude\QueryBuilder\on;

/**
 * Class CoachDatabaseQuery
 */
class CoachDatabaseQuery extends DatabaseQuery implements CoachQuery
{
    /**
     * @inheritDoc
     */
    public function filterActive(bool $active): self
    {
        $this->query->andWhere(
            CoachesTable::field('active')->eq($active)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query
            ->from(CoachesTable::name())
            ->join(
                MembersTable::name(),
                on(
                    MembersTable::column('id'),
                    CoachesTable::column('member_id')
                )
            )
            ->join(
                PersonsTable::name(),
                on(
                    PersonsTable::column('id'),
                    MembersTable::column('person_id')
                )
            )
        ;
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        return [
            ...CoachesTable::aliases(),
            ...PersonsTable::aliases()
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterId(int ...$ids): self
    {
        $this->query->andWhere(
            CoachesTable::field('id')->in(...$ids)
        );
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $coaches = new Collection();
        foreach ($rows as $row) {
            $coach = CoachesTable::createFromRow($row);
            $coaches->put(
                $coach->id,
                new CoachDTO(
                    $coach,
                    MembersTable::createFromRow($row),
                    PersonsTable::createFromRow($row)
                )
            );
        }

        return $coaches;
    }
}
