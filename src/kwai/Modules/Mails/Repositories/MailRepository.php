<?php
/**
 * User Repository interface
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Repositories;

use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
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
     * @throws NotFoundException
     * @throws RepositoryException
     */
    public function getById(int $id) : Entity;

    /**
     * Get the mail with the given uuid
     * @param  UniqueId $uid
     * @return Entity<Mail>
     * @throws RepositoryException
     * @throws NotFoundException
     */
    public function getByUUID(UniqueId $uid) : Entity;

    /**
     * Save a new Mail
     * @param  Mail $mail
     * @return Entity
     * @phpstan-return Entity<Mail>
     * @throws RepositoryException
     */
    public function create(Mail $mail): Entity;
}
