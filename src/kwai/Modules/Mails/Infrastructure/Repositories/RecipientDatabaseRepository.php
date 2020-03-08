<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Mails\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Infrastructure\Database;
use Kwai\Modules\Mails\Infrastructure\Mappers\RecipientMapper;
use Kwai\Modules\Mails\Infrastructure\RecipientsTable;
use Kwai\Modules\Mails\Repositories\RecipientRepository;
use Latitude\QueryBuilder\Query\SelectQuery;
use function Latitude\QueryBuilder\field;

/**
 * RecipientDatabaseRepository
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class RecipientDatabaseRepository implements RecipientRepository
{
    private Database\Connection $db;

    private RecipientsTable $table;

    public function __construct(Database\Connection $db)
    {
        $this->db = $db;
        $this->table = new RecipientsTable();
    }

    /**
     * @inheritDoc
     * @throws Database\DatabaseException
     * @throws NotFoundException
     */
    public function getById(int $id): Entity
    {
        $query = $this->createBaseQuery()
            ->where(field($this->table->column('id'))->eq(strval($id)))
            ->compile()
        ;
        $row = $this->db->execute($query)->fetch();
        if ($row) {
            return RecipientMapper::toDomain($row);
        }
        throw new NotFoundException('Recipient');
    }

    /**
     * @inheritDoc
     * @throws Database\DatabaseException
     */
    public function getForMails(array $mailIds): array
    {
        $query = $this->createBaseQuery()
            ->where(field($this->table->column('mail_id'))->eq(strval($mailIds)))
            ->compile()
        ;
        $rows = $this->db->execute($query)->fetchAll();
        return array_map(
            fn ($row) => RecipientMapper::toDomain($this->table->filter($row)),
            $rows
        );
    }

    /**
     * @inheritDoc
     * @throws Database\DatabaseException
     */
    public function create(array $recipients): array
    {
        $result = [];
        foreach ($recipients as $recipient) {
            $data = RecipientMapper::toPersistence($recipient);
            $query = $this->db->createQueryFactory()
                ->insert($this->table->from())
                ->columns(
                    ... array_keys($data)
                )
                ->values(
                    ... array_values($data)
                )
                ->compile()
            ;
            $this->db->execute($query);
            $result[] = new Entity(
                $this->db->lastInsertId(),
                $recipient
            );
        }
        return $result;
    }

    /**
     * Create the base SELECT query
     * @return SelectQuery
     */
    private function createBaseQuery(): SelectQuery
    {
        return $this->db->createQueryFactory()
            ->select(... $this->table->alias())
            ->from($this->table->from())
        ;
    }
}
