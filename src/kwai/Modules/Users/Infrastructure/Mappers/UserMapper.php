<?php
/**
 * Mapper for User entity.
 * @package kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\Name;
use Kwai\Core\Domain\ValueObjects\UniqueId;
use Kwai\Core\Domain\ValueObjects\EmailAddress;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\Timestamp;

use Kwai\Modules\Users\Domain\User;

/**
 * Mapper for the entity User
 */
final class UserMapper
{
    /**
     * Creates a User entity from a database row
     *
     * @param Collection $data
     * @return User
     */
    public static function toDomain(Collection $data): User
    {
        return new User(
            uuid: new UniqueId($data->get('uuid')),
            emailAddress: new EmailAddress($data->get('email')),
            abilities: $data->get('abilities', new Collection())->map(
                fn($ability) => new Entity((int) $ability->get('id'), AbilityMapper::toDomain($ability))
            ),
            traceableTime: new TraceableTime(
                Timestamp::createFromString($data->get('created_at')),
                $data->has('updated_at')
                    ? Timestamp::createFromString($data->get('updated_at'))
                    : null
            ),
            remark: $data->get('remark'),
            username: new Name(
            $data->get('first_name'),
            $data->get('last_name')
            ),
            member: $data->get('member_id')
        );
    }

    /**
     * Returns a data collection from a User domain object.
     *
     * @param User $user
     * @return Collection
     */
    public static function toPersistence(User $user): Collection
    {
        return collect([
            'uuid' => strval($user->getUuid()),
            'email' => strval($user->getEmailAddress()),
            'first_name' => $user->getUsername()->getFirstName(),
            'last_name' => $user->getUsername()->getLastName(),
            'remark' => $user->getRemark(),
            'member_id' => $user->getMember(),
            'created_at' => strval($user->getTraceableTime()->getCreatedAt()),
            'updated_at' => $user->getTraceableTime()->getUpdatedAt()?->__toString()
        ]);
    }
}
