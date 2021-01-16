<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types=1);

namespace Kwai\Modules\Users\Repositories;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\UserAccountNotFoundException;
use Kwai\Modules\Users\Domain\UserAccount;

/**
 * Interface UserAccountRepository
 */
interface UserAccountRepository
{
    /**
     * Get a UserAccount using the email address.
     *
     * @param EmailAddress $email
     * @throws RepositoryException
     * @throws UserAccountNotFoundException
     * @return Entity
     */
    public function get(EmailAddress $email): Entity;

    /**
     * Update the login information.
     *
     * @param Entity<UserAccount> $account
     * @throws RepositoryException
     */
    public function update(Entity $account): void;

    /**
     * Create a user account.
     * @param UserAccount $account
     * @return Entity
     * @throws RepositoryException
     */
    public function create(UserAccount $account): Entity;

    /**
     * Checks if a user with the given email already exists
     *
     * @param EmailAddress $email
     * @throws RepositoryException
     * @return bool
     */
    public function existsWithEmail(EmailAddress $email): bool;
}
