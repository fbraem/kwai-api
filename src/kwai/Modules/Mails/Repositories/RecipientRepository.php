<?php
/**
 * @package
 * @subpackage
 */
declare(strict_types=1);

namespace Kwai\Modules\Mails\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Modules\Mails\Domain\Recipient;

/**
 * Interface RecipientRepository
 * @SuppressWarnings(PHPMD.ShortVariable)
 */
interface RecipientRepository
{
    /**
     * Get the recipient with the given id
     * @param int $id
     * @return Entity<Recipient>
     */
    public function getById(int $id): Entity;

    /**
     * Get all recipients for the given mails
     * @param int[] $mailIds
     * @return Entity<Recipient>[]
     */
    public function getForMails(array $mailIds): array;

    /**
     * Create a recipient for the given mail entity
     * @param Entity $mail
     * @phpstan-param Entity<Mail> $mail
     * @param Recipient[] $recipients
     * @return Entity[]
     * @phpstan-return Entity<Recipient>[]
     */
    public function create(Entity $mail, array $recipients): array;
}