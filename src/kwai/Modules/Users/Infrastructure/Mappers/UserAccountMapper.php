<?php
/**
 * Mapper for UserAccount entity.
 * @package kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Mappers;

use Kwai\Core\Domain\Timestamp;
use Kwai\Core\Domain\Entity;

use Kwai\Modules\Users\Domain\UserAccount;
use Kwai\Modules\Users\Domain\ValueObjects\Password;

final class UserAccountMapper
{
    /**
     * Maps the table row object to UserAccount entity.
     * @param  object $raw
     * @return Entity<UserAccount>
     */
    public static function toDomain(object $raw): Entity
    {
        return new Entity(
            (int) $raw->id,
            new UserAccount((object)[
                'user' => UserMapper::toDomain($raw),
                'lastLogin' => isset($raw->last_login)
                    ? Timestamp::createFromString($raw->last_login)
                    : null,
                'lastUnsuccessfulLogin' => $raw->last_unsuccessful_login ?? null,
                'password' => new Password($raw->password),
                'revoked' => $raw->revoked ?? false
            ])
        );
    }

    /**
     * Returns a data array from a UserAccount entity.
     * @param  UserAccount $account
     * @return array
     * @noinspection PhpUndefinedMethodInspection
     */
    public static function toPersistence(UserAccount $account): array
    {
        if ($account->getUser()->getTraceableTime()->getUpdatedAt()) {
            $updated_at = strval(
                $account->getUser()->getTraceableTime()->getUpdatedAt()
            );
        } else {
            $updated_at = null;
        }

        //TODO: add last_unsuccessful_login
        //TODO: add revoked to table
        return [
            'last_login' => strval($account->getLastLogin()),
            'updated_at' => $updated_at,
            // 'revoked' => $account->isRevoked() ? '1' : '0'
        ];
    }
}
