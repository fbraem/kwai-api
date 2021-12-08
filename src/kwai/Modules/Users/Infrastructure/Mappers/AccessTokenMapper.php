<?php
/**
 * Mapper for AccessToken entity.
 * @package kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;

final class AccessTokenMapper
{
    public static function toDomain(Collection $data): AccessToken
    {
        return new AccessToken(
            identifier: new TokenIdentifier($data->get('identifier')),
            expiration: Timestamp::createFromString($data->get('expiration')),
            revoked: $data->get('revoked', 0) === 1,
            traceableTime: new TraceableTime(
                Timestamp::createFromString($data->get('created_at')),
                $data->has('updated_at') ? Timestamp::createFromString($data->get('updated_at')) : null
            ),
            account: new Entity(
                (int) $data->get('user')->get('id'),
                UserAccountMapper::toDomain($data->get('user'))
            )
        );
    }

    public static function toPersistence(AccessToken $accessToken): Collection
    {
        return collect([
            'identifier' => strval($accessToken->getIdentifier()),
            'expiration' => strval($accessToken->getExpiration()),
            'revoked' => $accessToken->isRevoked() ? 1 : 0,
            'created_at' => strval($accessToken->getTraceableTime()->getCreatedAt()),
            'updated_at' => $accessToken->getTraceableTime()->getUpdatedAt()?->__toString(),
            'user_id' => $accessToken->getUserAccount()->id()
        ]);
    }
}
