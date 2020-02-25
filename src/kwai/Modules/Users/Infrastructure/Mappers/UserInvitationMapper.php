<?php
/**
 * @package kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Mappers;


use Kwai\Core\Domain\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Timestamp;
use Kwai\Core\Domain\TraceableTime;
use Kwai\Core\Domain\UniqueId;
use Kwai\Modules\Users\Domain\UserInvitation;

/**
 * Class UserInvitationMapper
 * Maps a user invitation
 */
class UserInvitationMapper
{
    public static function toDomain(object $raw): Entity
    {
        return new Entity(
            (int) $raw->id,
            new UserInvitation((object) [
                'uuid' => new UniqueId($raw->uuid),
                'emailAddress' => new EmailAddress($raw->email),
                'expiration' => Timestamp::createFromString($raw->expired_at, $raw->expired_at_timezone),
                'name' => $raw->name,
                'creator' => UserMapper::toDomain($raw),
                'remark' => $raw->remark,
                'traceableTime' => new TraceableTime(
                    Timestamp::createFromString($raw->created_at),
                    isset($raw->updated_at)
                        ? Timestamp::createFromString($raw->updated_at)
                        : null
                )
            ])
        );
    }

    public static function toPersistence(UserInvitation $invitation): array
    {
        return [];
    }
}