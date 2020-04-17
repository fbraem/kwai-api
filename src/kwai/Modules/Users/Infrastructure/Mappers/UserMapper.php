<?php
/**
 * Mapper for User entity.
 * @package kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Mappers;

use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\Entity;

use Kwai\Modules\Users\Domain\User;
use Kwai\Modules\Users\Domain\ValueObjects\Username;

/**
 * Mapper for the entity User
 */
final class UserMapper
{
    /**
     * Creates a User entity from a database row
     * @param object $raw
     * @return Entity
     */
    public static function toDomain(object $raw): Entity
    {
        return new Entity(
            (int) $raw->id,
            new User((object)[
                'uuid' => new UniqueId($raw->uuid),
                'emailAddress' => new EmailAddress($raw->email),
                'traceableTime' => new TraceableTime(
                    Timestamp::createFromString($raw->created_at),
                    isset($raw->updated_at)
                        ? Timestamp::createFromString($raw->updated_at)
                        : null
                ),
                'remark' => $raw->remark,
                'username' => new Username($raw->first_name ?? null, $raw->last_name ?? null)
            ])
        );
    }

    /**
     * Returns a data array from a User domain object.
     * @param User $user
     * @return array
     */
    public static function toPersistence(User $user): array
    {
        if ($user->getTraceableTime()->getUpdatedAt()) {
            $updated_at = strval(
                $user->getTraceableTime()->getUpdatedAt()
            );
        } else {
            $updated_at = null;
        }

        return [
            'uuid' => strval($user->getUuid()),
            'email' => strval($user->getEmailAddress()),
            'first_name' => $user->getUsername()->getFirstName(),
            'last_name' => $user->getUsername()->getLastName(),
            'remark' => $user->getRemark(),
            'created_at' => strval($user->getTraceableTime()->getCreatedAt()),
            'updated_at' => $updated_at ? strval($updated_at) : null
        ];
    }
}
