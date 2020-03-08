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
     * @param Recipient[] $recipients
     * @return Entity<Recipient>[]
     */
    public function create(array $recipients): array;
}