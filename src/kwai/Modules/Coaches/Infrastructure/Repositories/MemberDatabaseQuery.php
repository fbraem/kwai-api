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
use Kwai\Modules\Coaches\Repositories\MemberQuery;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\on;

/**
 * Class MemberDatabaseQuery
 */
class MemberDatabaseQuery extends DatabaseQuery implements MemberQuery
{
    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query
            ->from((string) Tables::MEMBERS())
            ->join(
                (string) Tables::PERSONS(),
                on(Tables::PERSONS()->id, Tables::MEMBERS()->person_id)
            )
        ;
    }

    /**
     * @inheritDoc
     */
    protected function getColumns(): array
    {
        $memberAliasFn = Tables::MEMBERS()->getAliasFn();
        $personAliasFn = Tables::PERSONS()->getAliasFn();

        return [
            $memberAliasFn('id'),
            $personAliasFn('lastname'),
            $personAliasFn('firstname')
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterId(int $id): MemberQuery
    {
        $this->query->andWhere(
            field(Tables::MEMBERS()->id)->eq($id)
        );
        return $this;
    }

    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $members = new Collection();
        $filters = new Collection([
            Tables::MEMBERS()->getAliasPrefix(),
            Tables::PERSONS()->getAliasPrefix()
        ]);

        foreach ($rows as $row) {
            $rowCollection = new ColumnCollection($row);
            [
                $member,
                $person
            ] = $rowCollection->filter($filters);
            $member = $member->merge($person);
            $members->put($member->get('id'), $member);
        }

        return $members;
    }
}
