<?php
/**
 * @package kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Mappers;

use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Modules\Users\Domain\UserInvitation;

/**
 * Class UserInvitationMapper
 * Maps a user invitation
 */
class UserInvitationMapper
{
    /**
     * Create a UserInvitation entity from a database row
     * @param object $raw
     * @return Entity
     */
    public static function toDomain(object $raw): Entity
    {
        return new Entity(
            (int) $raw->id,
            new UserInvitation((object) [
                'uuid' => new UniqueId($raw->uuid),
                'emailAddress' => new EmailAddress($raw->email),
                'expiration' => Timestamp::createFromString($raw->expired_at, $raw->expired_at_timezone),
                'name' => $raw->name,
                'creator' => UserMapper::toDomain($raw->user),
                'remark' => $raw->remark,
                'confirmation' => $raw->confirmed_at ? Timestamp::createFromString($raw->confirmed_at) : null,
                'traceableTime' => new TraceableTime(
                    Timestamp::createFromString($raw->created_at),
                    isset($raw->updated_at)
                        ? Timestamp::createFromString($raw->updated_at)
                        : null
                )
            ])
        );
    }

    /**
     * Maps the UserInvitation to a table row
     * @param UserInvitation $invitation
     * @return array
     */
    public static function toPersistence(UserInvitation $invitation): array
    {
        if ($invitation->getTraceableTime()->isUpdated()) {
            $updated_at = strval($invitation->getTraceableTime()->getUpdatedAt());
        } else {
            $updated_at = null;
        }
        return [
            'uuid' => strval($invitation->getUniqueId()),
            'email' => strval($invitation->getEmailAddress()),
            'expired_at' => strval($invitation->getExpiration()),
            'expired_at_timezone' => strval($invitation->getExpiration()->getTimezone()),
            'confirmed_at' => $invitation->isConfirmed() ? strval($invitation->getConfirmation()) : null,
            'name' => $invitation->getName(),
            'user_id' => $invitation->getCreator()->id(),
            'remark' => $invitation->getRemark(),
            'created_at' => strval($invitation->getTraceableTime()->getCreatedAt()),
            'updated_at' => $updated_at
        ];
    }
}
