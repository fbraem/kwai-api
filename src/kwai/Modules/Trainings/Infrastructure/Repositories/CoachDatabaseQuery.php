<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Trainings\Infrastructure\Repositories;

use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\Trainings\Infrastructure\Mappers\CoachMapper;
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
        $memberAlias = Tables::MEMBERS()->getAliasFn();
        $personAlias = Tables::PERSONS()->getAliasFn();
        $userAlias = Tables::USERS()->getAliasFn();

        return [
            $coachAliasFn('id'),
            $coachAliasFn('description'),
            $coachAliasFn('diploma'),
            $coachAliasFn('active'),
            $coachAliasFn('remark'),
            $coachAliasFn('created_at'),
            $coachAliasFn('updated_at'),
            $memberAlias('id'),
            $memberAlias('license'),
            $memberAlias('license_end_date'),
            $memberAlias('remark'),
            $personAlias('lastname'),
            $personAlias('firstname'),
            $personAlias('gender'),
            $personAlias('birthdate'),
            $personAlias('firstname'),
            $userAlias('id'),
            $userAlias('first_name'),
            $userAlias('last_name')
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
        $rows = parent::execute($limit, $offset);
        if (count($rows) === 0) {
            return [];
        }

        $coachColumnFilter = Tables::COACHES()->createColumnFilter();
        $memberColumnFilter = Tables::MEMBERS()->createColumnFilter();
        $personColumnFilter = Tables::PERSONS()->createColumnFilter();
        $userColumnFilter = Tables::USERS()->createColumnFilter();

        $coaches = [];
        foreach ($rows as $row) {
            $coach = $coachColumnFilter->filter($row);
            $member = $memberColumnFilter->filter($row);
            $person = $personColumnFilter->filter($row);
            $coach->member = (object)array_merge((array)$member, (array)$person);
            $coach->creator = $userColumnFilter->filter($row);

            $coaches[$coach->id] = CoachMapper::toDomain($coach);
        }

        return $coaches;
    }
}
