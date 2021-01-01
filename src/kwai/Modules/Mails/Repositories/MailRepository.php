<?php
/**
 * User Repository interface
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Repositories;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Database\QueryException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Mails\Domain\Exceptions\MailNotFoundException;
use Kwai\Modules\Mails\Domain\Mail;

/**
 * Mail repository interface
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
interface MailRepository
{
    /**
     * Get the email with the given id
     * @param  int    $id
     * @return Entity
     * @phpstan-return Entity<Mail>
     * @throws MailNotFoundException
     * @throws RepositoryException
     */
    public function getById(int $id) : Entity;

    /**
     * Save a new Mail
     * @param  Mail $mail
     * @return Entity
     * @phpstan-return Entity<Mail>
     * @throws RepositoryException
     */
    public function create(Mail $mail): Entity;

    /**
     * @param MailQuery $query
     * @param int|null  $limit
     * @param int|null  $offset
     * @return Collection
     * @throws QueryException
     */
    public function getAll(MailQuery $query, ?int $limit = null, ?int $offset = null) : Collection;
}
