<?php
/**
 * @package Modules
 * @subpackage Club
 */
declare(strict_types=1);

namespace Kwai\Modules\Club\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Infrastructure\Database\Connection;
use Kwai\Core\Infrastructure\Database\DatabaseQuery;
use Kwai\Modules\Club\Infrastructure\ContactsTable;
use Kwai\Modules\Club\Infrastructure\Mappers\MemberDTO;
use Kwai\Modules\Club\Infrastructure\MembersTable;
use Kwai\Modules\Club\Infrastructure\PersonsTable;
use Kwai\Modules\Club\Repositories\MemberQuery;
use function Latitude\QueryBuilder\on;

/**
 * Class MemberDatabaseQuery
 */
class MemberDatabaseQuery extends DatabaseQuery implements MemberQuery
{
    public function __construct(Connection $db)
    {
        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query
            ->from(MembersTable::name())
            ->join(
                PersonsTable::name(),
                on(
                    PersonsTable::column('id'),
                    MembersTable::column('person_id')
                )
            )
            ->join(
                ContactsTable::name(),
                on(
                    ContactsTable::column('id'),
                    PersonsTable::column('contact_id')
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
            ...MembersTable::aliases(),
            ...ContactsTable::aliases(),
            ...PersonsTable::aliases()
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterId(int $id): MemberQuery
    {
        $this->query->andWhere(
            MembersTable::field('id')->eq($id)
        );
        return $this;
    }

    /**
     * @inheritDoc
     * @returns Collection<MemberDTO>
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $members = new Collection();

        foreach ($rows as $row) {
            $member = MembersTable::createFromRow($row);
            $members->put(
                $member->id,
                new MemberDTO(
                    $member,
                    ContactsTable::createFromRow($row),
                    PersonsTable::createFromRow($row)
                )
            );
        }

        return $members;
    }
}
