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
use Kwai\Modules\Club\Infrastructure\ContactsTableSchema;
use Kwai\Modules\Club\Infrastructure\Mappers\MemberDTO;
use Kwai\Modules\Club\Infrastructure\MembersTableSchema;
use Kwai\Modules\Club\Infrastructure\PersonsTableSchema;
use Kwai\Modules\Club\Repositories\MemberQuery;
use function Latitude\QueryBuilder\on;

/**
 * Class MemberDatabaseQuery
 */
class MemberDatabaseQuery extends DatabaseQuery implements MemberQuery
{
    private MembersTableSchema $membersSchema;
    private ContactsTableSchema $contactsSchema;
    private PersonsTableSchema $personsSchema;

    public function __construct(Connection $db)
    {
        $this->membersSchema = new MembersTableSchema();
        $this->contactsSchema = new ContactsTableSchema();
        $this->personsSchema = new PersonsTableSchema();

        parent::__construct($db);
    }

    /**
     * @inheritDoc
     */
    protected function initQuery(): void
    {
        $this->query
            ->from(MembersTableSchema::getTableName())
            ->join(
                PersonsTableSchema::getTableName(),
                on(
                    $this->personsSchema->getColumn('id'),
                    $this->membersSchema->getColumn('person_id')
                )
            )
            ->join(
                $this->contactsSchema::getTableName(),
                on(
                    $this->contactsSchema->getColumn('id'),
                    $this->personsSchema->getColumn('contact_id')
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
            ...$this->membersSchema->getAllAliases(),
            ...$this->contactsSchema->getAllAliases(),
            ...$this->personsSchema->getAllAliases()
        ];
    }

    /**
     * @inheritDoc
     */
    public function filterId(int $id): MemberQuery
    {
        $this->query->andWhere(
            $this->membersSchema->field('id')->eq($id)
        );
        return $this;
    }

    /**
     * @inheritDoc
     * @returns Collection<string,MemberDTO>
     */
    public function execute(?int $limit = null, ?int $offset = null): Collection
    {
        $rows = parent::walk($limit, $offset);

        $members = new Collection();

        foreach ($rows as $row) {
            $member = $this->membersSchema->map($row);
            $contact = $this->contactsSchema->map($row);
            $person = $this->personsSchema->map($row);
            $members->put(
                $member->id,
                new MemberDTO(
                    $member,
                    $contact,
                    $person
                )
            );
        }

        return $members;
    }
}
