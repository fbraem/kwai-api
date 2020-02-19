<?php
/**
 * Mapper for UserAccount entity.
 * @package kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Mappers;

use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Domain\EmailAddress;
use Kwai\Core\Domain\TraceableTime;
use Kwai\Core\Domain\Timestamp;
use Kwai\Core\Domain\Entity;

use Kwai\Modules\Users\Domain\UserAccount;
use Kwai\Modules\Users\Domain\ValueObjects\Username;
use Kwai\Modules\Users\Domain\ValueObjects\Password;

final class UserAccountMapper
{
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

    public static function toPersistence(Entity $user): object
    {
    }
}
