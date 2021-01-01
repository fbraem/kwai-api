<?php
/**
 * Mail Repository.
 * @package kwai
 * @subpackage Mails
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Infrastructure\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\DatabaseRepository;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Mails\Domain\Exceptions\MailNotFoundException;
use Kwai\Modules\Mails\Domain\Mail;
use Kwai\Modules\Mails\Infrastructure\Mappers\MailMapper;
use Kwai\Modules\Mails\Infrastructure\Tables;
use Kwai\Modules\Mails\Repositories\MailQuery;
use Kwai\Modules\Mails\Repositories\MailRepository;

/**
 * Class MailDatabaseRepository
 *
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
final class MailDatabaseRepository extends DatabaseRepository implements MailRepository
{
    /**
     * @inheritdoc
     */
    public function getById(int $id) : Entity
    {
        $query = $this->createQuery()->filterId($id);

        try {
            $entities = $this->getAll($query);
        } catch (QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }

        if ($entities->isNotEmpty()) {
            return $entities[$id];
        }

        throw new MailNotFoundException($id);
    }

    /**
     * @inheritdoc
     */
    public function create(Mail $mail): Entity
    {
        $data = MailMapper::toPersistence($mail);

        $query = $this->db->createQueryFactory()
            ->insert((string) Tables::MAILS())
            ->columns(... $data->keys())
            ->values(... $data->values()
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
     * Create the query
     *
     * @return MailQuery
     */
    private function createQuery(): MailQuery
    {
        return new MailDatabaseQuery($this->db);
    }

    /**
     * @inheritDoc
     */
    public function getAll(MailQuery $query, ?int $limit = null, ?int $offset = null): Collection
    {
        $mails = $query->execute($limit, $offset);
        return $mails->mapWithKeys(
            fn($item, $key) => [
                $key => new Entity((int) $key, MailMapper::toDomain($item))
            ]
        );
    }
}
