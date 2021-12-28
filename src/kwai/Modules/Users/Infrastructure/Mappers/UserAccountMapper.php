<?php
/**
 * Mapper for UserAccount entity.
 * @package kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Modules\Users\Domain\UserAccount;
use Kwai\Modules\Users\Domain\ValueObjects\Password;

final class UserAccountMapper
{
    /**
     * Maps the table row object to UserAccount entity.
     *
     * @param Collection $data
     * @return UserAccount
     */
    public static function toDomain(Collection $data): UserAccount
    {
        return new UserAccount(
            user: UserMapper::toDomain($data),
            lastLogin: $data->has('last_login')
                ? Timestamp::createFromString($data->get('last_login'))
                : null,
            lastUnsuccessfulLogin: $data->has('last_unsuccessful_login')
                ? Timestamp::createFromString($data->get('last_unsuccessful_login'))
                : null,
            password: new Password($data->get('password')),
            revoked: $data->get('revoked', 0) === 1
        );
    }

    /**
     * Returns a data array from a UserAccount entity.
     *
     * @param UserAccount $account
     * @return Collection
     */
    public static function toPersistence(UserAccount $account): Collection
    {
        $lastLogin = $account->getLastLogin();
        $lastUnsuccessfulLogin = $account->getLastUnsuccessFulLogin();

        return collect([
            'last_unsuccessful_login' => $lastUnsuccessfulLogin ? strval($lastUnsuccessfulLogin) : null,
            'revoked' => $account->isRevoked() ? 1 : 0,
            'last_login' => $lastLogin ? strval($lastLogin) : null,
            'password' => strval($account->getPassword()),
        ])->merge(UserMapper::toPersistence($account->getUser()));
    }
}
