<?php
/**
 * Mail Repository.
 * @package kwai
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Infrastructure\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Database;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Mails\Domain\Mail;
use Kwai\Modules\Mails\Infrastructure\Mappers\MailMapper;
use Kwai\Modules\Mails\Infrastructure\Tables;
use Kwai\Modules\Mails\Repositories\MailRepository;
use Kwai\Modules\Users\Infrastructure\Tables as UsersTables;
use Latitude\QueryBuilder\Query\SelectQuery;
use function Latitude\QueryBuilder\field;
use function Latitude\QueryBuilder\on;

/**
 * Class MailDatabaseRepository
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
final class MailDatabaseRepository extends Database\DatabaseRepository implements MailRepository
{
    /**
     * @inheritdoc
     */
    public function getById(int $id) : Entity
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $query = $this->createBaseQuery()
            ->where(field(Tables::MAILS()->id)->eq(strval($id)))
        ;

        try {
            $row = $this->db->execute($query)->fetch();
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        if ($row) {
            $mailRow = Tables::MAILS()->createColumnFilter()->filter($row);
            $mailRow->user = UsersTables::USERS()->createColumnFilter()->filter($row);
            return MailMapper::toDomain($mailRow);
        }
        throw new NotFoundException('Mail');
    }

    /**
     * @inheritdoc
     */
    public function getByUUID(UniqueId $uid) : Entity
    {
        /** @noinspection PhpUndefinedFieldInspection */
        $query = $this->createBaseQuery()
            ->where(field(Tables::MAILS()->uuid)->eq(strval($uid)))
        ;

        try {
            $row = $this->db->execute($query)->fetch();
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        if ($row) {
            $mailRow = Tables::MAILS()->createColumnFilter()->filter($row);
            $mailRow->user = UsersTables::USERS()->createColumnFilter()->filter($row);
            return MailMapper::toDomain($mailRow);
        }
        throw new NotFoundException('Mail');
    }

    /**
     * @inheritdoc
     */
    public function create(Mail $mail): Entity
    {
        $data = MailMapper::toPersistence($mail);

        $query = $this->db->createQueryFactory()
            ->insert((string) Tables::MAILS())
            ->columns(
                ... array_keys($data)
            )
            ->values(
                ... array_values($data)
            )
        ;
        try {
            $this->db->execute($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        return new Entity(
            $this->db->lastInsertId(),
            $mail
        );
    }

    /**
     * Create the base SELECT query
     * @return SelectQuery
     */
    private function createBaseQuery(): SelectQuery
    {
        $aliasMailFn = Tables::MAILS()->getAliasFn();
        $aliasUserFn = UsersTables::USERS()->getAliasFn();

        /** @noinspection PhpUndefinedFieldInspection */
        return $this->db->createQueryFactory()
            ->select(
                $aliasMailFn('id'),
                $aliasMailFn('tag'),
                $aliasMailFn('uuid'),
                $aliasMailFn('sender_email'),
                $aliasMailFn('sender_name'),
                $aliasMailFn('subject'),
                $aliasMailFn('html_body'),
                $aliasMailFn('text_body'),
                $aliasMailFn('sent_time'),
                $aliasMailFn('remark'),
                $aliasMailFn('user_id'),
                $aliasMailFn('created_at'),
                $aliasMailFn('updated_at'),
                $aliasUserFn('id'),
                $aliasUserFn('email'),
                $aliasUserFn('first_name'),
                $aliasUserFn('last_name'),
                $aliasUserFn('remark'),
                $aliasUserFn('uuid'),
                $aliasUserFn('created_at'),
                $aliasUserFn('updated_at')
            )
            ->from((string) Tables::MAILS())
            ->join(
                (string) UsersTables::USERS(),
                on(
                    Tables::MAILS()->user_id,
                    UsersTables::USERS()->id
                )
            )
        ;
    }
}
