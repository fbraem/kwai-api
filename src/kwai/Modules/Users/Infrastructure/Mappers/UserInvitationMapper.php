<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Infrastructure\Mappers\CreatorMapper;
use Kwai\Modules\Users\Domain\UserInvitation;

/**
 * Class UserInvitationMapper
 * Maps a user invitation
 */
class UserInvitationMapper
{
    /**
     * Create a UserInvitation entity from a database row
     *
     * @param Collection $data
     * @return UserInvitation
     */
    public static function toDomain(Collection $data): UserInvitation
    {
        return new UserInvitation(
            uuid: new UniqueId($data->get('uuid')),
            emailAddress: new EmailAddress($data->get('email')),
            expiration: Timestamp::createFromString($data->get('expired_at'), $data->get('expired_at_timezone')),
            name: $data->get('name'),
            creator: CreatorMapper::toDomain($data->get('creator')),
            revoked: $data->get('revoked', '0') === '1',
            remark: $data->get('remark'),
            confirmation: $data->has('confirmed_at') ? Timestamp::createFromString($data->get('confirmed_at')) : null,
            traceableTime: new TraceableTime(
                Timestamp::createFromString($data->get('created_at')),
                $data->has('updated_at')
                    ? Timestamp::createFromString($data->get('updated_at'))
                    : null
            )
        );
    }

    /**
     * Maps the UserInvitation to a table row
     *
     * @param UserInvitation $invitation
     * @return Collection
     */
    public static function toPersistence(UserInvitation $invitation): Collection
    {
        return collect([
            'uuid' => strval($invitation->getUniqueId()),
            'email' => strval($invitation->getEmailAddress()),
            'expired_at' => strval($invitation->getExpiration()),
            'expired_at_timezone' => strval($invitation->getExpiration()->getTimezone()),
            'confirmed_at' => $invitation->isConfirmed() ? strval($invitation->getConfirmation()) : null,
            'name' => $invitation->getName(),
            'user_id' => $invitation->getCreator()->getId(),
            'remark' => $invitation->getRemark(),
            'created_at' => strval($invitation->getTraceableTime()->getCreatedAt()),
            'updated_at' => $invitation->getTraceableTime()->getUpdatedAt()?->__toString()
        ]);
    }
}
