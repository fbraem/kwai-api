<?php
/**
 * Mapper for UserAccount entity.
 * @package kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Mappers;

use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Modules\Users\Domain\UserAccount;
use Kwai\Modules\Users\Domain\UserAccountEntity;
use Kwai\Modules\Users\Domain\ValueObjects\Password;
use Kwai\Modules\Users\Infrastructure\UsersTable;

final class UserAccountDTO
{
    public function __construct(
        public UsersTable $user = new UsersTable()
    ) {
    }

    /**
     * Create a UserAccount domain object from a database row.
     *
     * @return UserAccount
     */
    public function create(): UserAccount
    {
        return new UserAccount(
            user: (new UserDTO($this->user))->create(),
            password: new Password($this->user->password),
            lastLogin: $this->user->last_login
                ? Timestamp::createFromString($this->user->last_login)
                : null,
            lastUnsuccessfulLogin: $this->user->last_unsuccessful_login
                ? Timestamp::createFromString($this->user->last_unsuccessful_login)
                : null,
            revoked: $this->user->revoked === 1
        );
    }

    /**
     * Create a UserAccount entity from a database row.
     *
     * @return UserAccountEntity
     */
    public function createEntity(): UserAccountEntity
    {
        return new UserAccountEntity(
            $this->user->id,
            $this->create()
        );
    }

    /**
     * Persist a UserAccount domain to a database row.
     *
     * @param UserAccount $account
     * @return $this
     */
    public function persist(UserAccount $account): UserAccountDTO
    {
        (new UserDTO($this->user))
            ->persist($account->getUser());
        $lastLogin = $account->getLastLogin();
        if ($lastLogin) {
            $this->user->last_login = (string) $lastLogin;
        }
        $lastUnsuccessfulLogin = $account->getLastUnsuccessFulLogin();
        if ($lastUnsuccessfulLogin) {
            $this->user->last_unsuccessful_login = (string) $lastUnsuccessfulLogin;
        }
        $this->user->revoked = $account->isRevoked() ? 1 : 0;
        $this->user->password = (string) $account->getPassword();

        return $this;
    }

    /**
     * Persist the account entity to a database row.
     *
     * @param UserAccountEntity $account
     * @return $this
     */
    public function persistEntity(UserAccountEntity $account): UserAccountDTO
    {
        $this->user->id = $account->id();
        return $this->persist($account->domain());
    }
}
