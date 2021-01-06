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
 * Interface UserQuery
 */
interface UserQuery extends Query
{
    /**
     * Filter a user by the id
     *
     * @param int $id
     * @return $this
     */
    public function filterById(int $id): self;

    /**
     * Filter a user by the unique id (UUID)
     *
     * @param UniqueId $uuid
     * @return $this
     */
    public function filterByUUID(UniqueId $uuid): self;

    /**
     * Filter a user by the email address
     *
     * @param EmailAddress $email
     * @return $this
     */
    public function filterByEmail(EmailAddress $email): self;
}
