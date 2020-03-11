<?php
/**
 * Mapper for User entity.
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
     * Create a database object from a User entity
     * @param Entity $user
     * @return object
     */
    public static function toPersistence(Entity $user): object
    {
        //TODO: implement
        return (object)[];
    }
}
