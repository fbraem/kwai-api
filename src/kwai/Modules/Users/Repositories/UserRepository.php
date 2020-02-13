<?php
/**
 * User Repository interface
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Repositories;

use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\EmailAddress;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Domain\User;

/**
 * User repository interface
 */
interface UserRepository
{
    /**
     * Get the user with the given id
     * @param  int    $id
     * @return Entity<User>
     */
    public function getById(int $id) : Entity;

    /**
     * Get the user with the given uuid
     * @param  UniqueId $uid
     * @return Entity<User>
     */
    public function getByUUID(UniqueId $uid) : Entity;

    /**
     * Get the user with the given emailaddress.
     * @param  EmailAddress $email
     * @return Entity<User>
     */
    public function getByEmail(EmailAddress $email) : Entity;

    /**
     * Get the user associated with the given token identifier
     * (from an accesstoken).
     * @param  TokenIdentifier $token
     * @return Entity<User>
     */
    public function getByAccessToken(TokenIdentifier $token) : Entity;
}
