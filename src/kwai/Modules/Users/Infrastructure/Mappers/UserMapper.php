<?php
/**
 * User Repository.
 * @package kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Mappers;

use Kwai\Core\Domain\UniqueId;
use Kwai\Core\Domain\EmailAddress;
use Kwai\Core\Domain\TraceableTime;
use Kwai\Core\Domain\DateTime;

use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\ValueObjects\Username;
use Kwai\Modules\Users\Domain\ValueObjects\Password;

final class UserMapper
{
    public static function toDomain(object $raw): User
    {
        return User::create((object)[
            'uuid' => new UniqueId($raw->uuid),
            'emailAddress' => new EmailAddress($raw->email),
            'traceableTime' => new TraceableTime(
                DateTime::createFromString($raw->created_at),
                isset($raw->updated_at) ? DateTime::createFromString($raw->updated_at) : null
            ),
            'lastLogin' => isset($raw->last_login) ? DateTime::createFromString($raw->last_login) : null,
            'remark' => $raw->remark,
            'username' => new Username($raw->first_name, $raw->last_name),
            'password' => new Password($raw->password)
        ], $raw->id);
    }

    public static function toPersistence(User $user): object
    {
    }
}
