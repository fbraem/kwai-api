<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Repositories;

use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Repositories\Query;

/**
 * Interface UserInvitationQuery
 */
interface UserInvitationQuery extends Query
{
    /**
     * Filter on unique id
     *
     * @param UniqueId $uuid
     * @return UserInvitationQuery
     */
    public function filterByUniqueId(UniqueId $uuid): self;

    /**
     * Filter the invitations for the given email
     *
     * @param EmailAddress $emailAddress
     * @return UserInvitationQuery
     */
    public function filterByEmail(EmailAddress $emailAddress): self;

    /**
     * Filter only the invitations that are still active (not expired)
     * on the given date.
     *
     * @note The expiration date is stored as UTC, so don't forget to set
     *       the timezone of the timestamp. The implementation of this query
     *       should convert the date to UTC before applying the filter.
     *
     * @param Timestamp $timestamp
     * @return UserInvitationQuery
     */
    public function filterActive(Timestamp $timestamp): self;

    /**
     * Filter only the confirmed or not confirmed invitations.
     *
     * @param bool $confirmed
     * @return UserInvitationQuery
     */
    public function filterConfirmed(bool $confirmed): self;
}
