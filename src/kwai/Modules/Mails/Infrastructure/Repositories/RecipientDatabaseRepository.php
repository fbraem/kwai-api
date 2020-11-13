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
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Mails\Infrastructure\Mappers\RecipientMapper;
use Kwai\Modules\Mails\Infrastructure\Tables;
use Kwai\Modules\Mails\Repositories\RecipientRepository;
use Latitude\QueryBuilder\Query\SelectQuery;
use function Latitude\QueryBuilder\field;

/**
 * RecipientDatabaseRepository
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
class RecipientDatabaseRepository extends Database\DatabaseRepository implements RecipientRepository
{
    /**
     * @inheritDoc
     */
    public function getById(int $id): Entity
    {
        $query = $this->createBaseQuery()
            ->where(field('id')->eq(strval($id)))
        ;
        try {
            $row = $this->db->execute($query)->fetch();
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
        if ($row) {
            return RecipientMapper::toDomain(Tables::RECIPIENTS()->createColumnFilter()->filter($row));
        }
        throw new NotFoundException('Recipient');
    }

    /**
     * @inheritDoc
     */
    public function getForMails(array $mailIds): array
    {
        $query = $this->createBaseQuery()
            ->where(field('mail_id')->eq(strval($mailIds)))
        ;

        try {
            $rows = $this->db->execute($query)->fetchAll();
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        $columnFilter = Tables::RECIPIENTS()->createColumnFilter();
        return array_map(
            fn ($row) => RecipientMapper::toDomain($columnFilter->filter($row)),
            $rows
        );
    }

    /**
     * @inheritDoc
     */
    public function create(Entity $mail, array $recipients): array
    {
        $result = [];
        foreach ($recipients as $recipient) {
            $data = RecipientMapper::toPersistence($recipient);
            $query = $this->db->createQueryFactory()
                ->insert((string) Tables::RECIPIENTS())
                ->columns(
                    'mail_id',
                    ... array_keys($data)
                )
                ->values(
                    $mail->id(),
                    ... array_values($data)
                )
            ;
            try {
                $this->db->execute($query);
            } catch (QueryException $e) {
                throw new RepositoryException(__METHOD__, $e);
            }
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
        $aliasFn = Tables::RECIPIENTS()->getAliasFn();
        return $this->db->createQueryFactory()
            ->select(
                $aliasFn('id'),
                $aliasFn('mail_id'),
                $aliasFn('type'),
                $aliasFn('email'),
                $aliasFn('name')
            )
            ->from((string) Tables::RECIPIENTS())
        ;
    }
}
