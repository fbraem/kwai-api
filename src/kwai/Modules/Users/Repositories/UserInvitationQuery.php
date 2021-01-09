<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Repositories;

use Kwai\Core\Domain\ValueObjects\EmailAddress;
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
}
