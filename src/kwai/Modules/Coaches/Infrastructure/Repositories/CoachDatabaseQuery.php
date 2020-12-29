<?php
/**
 * @package Modules
 * @subpackage Coaches
 */
declare(strict_types=1);

namespace Kwai\Modules\Coaches\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Database\ColumnCollection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\Coaches\Infrastructure\Tables;
use Kwai\Modules\Coaches\Repositories\CoachQuery;
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
    public function filterActive(bool $active): self
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::COACHES()->active)->eq($active)
        );
        return $this;
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
        $memberAliasFn = Tables::MEMBERS()->getAliasFn();
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
            $memberAliasFn('id'),
            $personAlias('lastname'),
            $personAlias('firstname'),
            $userAlias('id'),
            $userAlias('first_name'),
            $userAlias('last_name')
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterIds(int ...$ids): self
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::COACHES()->id)->in(...$ids)
        );
        return $this;
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
           Tables::MEMBERS()->getAliasPrefix(),
           Tables::PERSONS()->getAliasPrefix(),
           Tables::USERS()->getAliasPrefix()
        ]);
        foreach ($rows as $row) {
            $rowCollection = new ColumnCollection($row);
            [
                $coach,
                $member,
                $person,
                $user
            ] = $rowCollection->filter($filters);

            $member = $member->merge($person);
            $coach = $coach->put('member', $member);
            $coach->put('creator', $user);
            $coaches->put($coach->get('id'), $coach);
        }

        return $coaches;
    }

    public function filterMember(int $memberId): self
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $this->query->andWhere(
            field(Tables::COACHES()->member_id)->eq($memberId)
        );

        return $this;
    }
}
