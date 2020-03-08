<?php
/**
 * Mail Repository.
 * @package kwai
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Infrastructure\Database;

use Kwai\Modules\Mails\Domain\Mail;
use Kwai\Modules\Mails\Repositories\MailRepository;
use Kwai\Modules\Mails\Infrastructure\Mappers\MailMapper;
use Kwai\Modules\Mails\Infrastructure\MailsTable;
use Kwai\Modules\Mails\Infrastructure\RecipientsTable;

use Kwai\Modules\Users\Infrastructure\UsersTable;

use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\on;
use Latitude\QueryBuilder\Query\SelectQuery;

/**
 * Class MailDatabaseRepository
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
final class MailDatabaseRepository implements MailRepository
{
    private Database\Connection $db;

    private MailsTable $table;

    private UsersTable $usersTable;

    private RecipientsTable $recipientsTable;

    public function __construct(Database\Connection $db)
    {
        $this->db = $db;
        $this->table = new MailsTable();
        $this->usersTable = new UsersTable();
        $this->recipientsTable = new RecipientsTable();
    }

    /**
     * @inheritdoc
     * @throws Database\DatabaseException
     * @throws NotFoundException
     */
    public function getById(int $id) : Entity
    {
        $query = $this->createBaseQuery()
            ->where(field($this->table->from() . '.id')->eq(strval($id)))
            ->compile()
        ;

        $row = $this->db->execute($query)->fetch();
        if ($row) {
            $mailRow = $this->table->filter($row);
            $mailRow->user = $this->usersTable->filter($row);
            return MailMapper::toDomain($mailRow);
        }
        throw new NotFoundException('Mail');
    }

    /**
     * @inheritdoc
     * @throws Database\DatabaseException
     * @throws NotFoundException
     */
    public function getByUUID(UniqueId $uid) : Entity
    {
        $query = $this->createBaseQuery()
            ->where(field($this->table->from() . '.uuid')->eq(strval($uid)))
            ->compile()
        ;

        $row = $this->db->execute($query)->fetch();
        if ($row) {
            $mailRow = $this->table->filter($row);
            $mailRow->user = $this->usersTable->filter($row);
            return MailMapper::toDomain($mailRow);
        }
        throw new NotFoundException('Mail');
    }

    /**
     * @inheritdoc
     * @throws Database\DatabaseException
     */
    public function create(Mail $mail): Entity
    {
        $data = MailMapper::toPersistence($mail);

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
        $mailId = $this->db->lastInsertId();
        return new Entity(
            $mailId,
            $mail
        );
    }

    /**
     * Create the base SELECT query
     * @return SelectQuery
     */
    private function createBaseQuery(): SelectQuery
    {
        $columns = array_merge(
            $this->table->alias(),
            $this->usersTable->alias()
        );

        return $this->db->createQueryFactory()
            ->select(... $columns)
            ->from($this->table->from())
            ->join(
                $this->usersTable->from(),
                on(
                    $this->table->column('user_id'),
                    $this->usersTable->column('.id')
                )
            )
        ;
    }
}
