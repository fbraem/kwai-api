<?php
/**
 * User Repository interface
 */
declare(strict_types = 1);

namespace Kwai\Modules\Mails\Repositories;

use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\EmailAddress;
use Kwai\Modules\Mails\Domain\Mail;

/**
 * Mail repository interface
 */
interface MailRepository
{
    /**
     * Get the email with the given id
     * @param  int    $id
     * @return Entity<Mail>
     */
    public function getById(int $id) : Entity;

    /**
     * Get the mail with the given uuid
     * @param  UniqueId $uid
     * @return Entity<Mail>
     */
    public function getByUUID(UniqueId $uid) : Entity;

    /**
     * Save a new Mail
     * @param  Mail $mail
     * @return Entity<Mail>
     */
    public function create(Mail $mail): Entity;
}
