<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Database\ColumnCollection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\Trainings\Infrastructure\Tables;
use Kwai\Modules\Trainings\Repositories\CoachQuery;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\on;

/**
 * Class CoachDatabaseQuery
 */
class CoachDatabaseQuery extends DatabaseQuery implements CoachQuery
{
    /**
     * @inheritDoc
     */
    public function filterActive(bool $active): void
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::COACHES()->active)->eq($active)
        );
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query
            ->from((string) Tables::COACHES())
            ->join(
                (string) Tables::MEMBERS(),
                on(Tables::MEMBERS()->id, Tables::COACHES()->member_id)
            )
            ->join(
                (string) Tables::PERSONS(),
                on(Tables::PERSONS()->id, Tables::MEMBERS()->person_id)
            )
            ->join(
                (string) Tables::USERS(),
                on(Tables::USERS()->id, Tables::COACHES()->user_id)
            )
        ;
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        $coachAliasFn = Tables::COACHES()->getAliasFn();
        $personAlias = Tables::PERSONS()->getAliasFn();

        return [
            $coachAliasFn('id'),
            $personAlias('lastname'),
            $personAlias('firstname')
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterId(int $id): void
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::COACHES()->id)->eq($id)
        );
    }

    /**
     * @inheritDoc
     */
    public function execute(?int $limit = null, ?int $offset = null)
    {
        $rows = parent::walk($limit, $offset);

        $coaches = new Collection();
        $filters = new Collection([
           Tables::COACHES()->getAliasPrefix(),
           Tables::PERSONS()->getAliasPrefix()
        ]);
        foreach ($rows as $row) {
            $rowCollection = new ColumnCollection($row);
            [
                $coach,
                $person
            ] = $rowCollection->filter($filters);

            $coach->merge($person);
            $coaches->put($coach->get('id'), $coach);
        }

        return $coaches;
    }
}
