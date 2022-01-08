<?php
/**
 * Mapper for UserAccount entity.
 * @package kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Mappers;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Modules\Users\Domain\UserAccount;
use Kwai\Modules\Users\Domain\ValueObjects\Password;
use Kwai\Modules\Users\Infrastructure\UsersTableSchema;

final class UserAccountDTO
{
    public function __construct(
        public UsersTableSchema $user = new UsersTableSchema()
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
            lastLogin: $this->user->last_login
                ? Timestamp::createFromString($this->user->last_login)
                : null,
            lastUnsuccessfulLogin: $this->user->last_unsuccessful_login
                ? Timestamp::createFromString($this->user->last_unsuccessful_login)
                : null,
            password: new Password($this->user->password),
            revoked: $this->user->revoked === 1
        );
    }

    /**
     * Create a UserAccount entity from a database row.
     *
     * @return Entity<UserAccount>
     */
    public function createEntity(): Entity
    {
        return new Entity(
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
    public function persist(UserAccount $account): static
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
     * @param Entity<UserAccount> $account
     * @return $this
     */
    public function persistEntity(Entity $account): static
    {
        $this->user->id = $account->id();
        return $this->persist($account->domain());
    }
}
