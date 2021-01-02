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
use Kwai\Modules\Mails\Domain\Recipient;
use Kwai\Modules\Mails\Infrastructure\Mappers\MailMapper;
use Kwai\Modules\Mails\Infrastructure\Mappers\RecipientMapper;
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

        // Insert mail
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

        $entity = new Entity(
            $this->db->lastInsertId(),
            $mail
        );

        // Insert all recipients
        $this->insertRecipients($entity);

        return $entity;
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
    public function getAll(MailQuery $query = null, ?int $limit = null, ?int $offset = null): Collection
    {
        $query ??= $this->createQuery();

        $mails = $query->execute($limit, $offset);
        return $mails->mapWithKeys(
            fn($item, $key) => [
                $key => new Entity((int) $key, MailMapper::toDomain($item))
            ]
        );
    }

    /**
     * Insert recipients for a mail
     *
     * @param Entity $mail
     * @throws RepositoryException
     */
    private function insertRecipients(Entity $mail)
    {
        /* @var Collection $recipients */
        /** @noinspection PhpUndefinedMethodInspection */
        $recipients = $mail->getRecipients();
        if ($recipients->count() === 0)
            return;

        $recipients
            ->transform(
            fn(Recipient $recipient) => RecipientMapper::toPersistence($recipient)
            )
            ->map(
                fn(Collection $item) => $item->put('mail_id', $mail->id())
            )
        ;

        $query = $this->db->createQueryFactory()
            ->insert((string) Tables::RECIPIENTS())
            ->columns(... $recipients->first()->keys())
        ;
        $recipients->each(
            fn(Collection $recipient) => $query->values(... $recipient->values())
        );

        try {
            $this->db->execute($query);
        } catch(QueryException $e) {
            throw new RepositoryException(__METHOD__, $e);
        }
    }
}
