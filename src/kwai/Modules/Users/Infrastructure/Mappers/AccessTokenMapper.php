<?php
/**
 * Mapper for AccessToken entity.
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

use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;

final class AccessTokenMapper
{
    public static function toDomain(object $raw): Entity
    {
        return new Entity(
            (int) $raw->id,
            new AccessToken((object)[
                'identifier' => new TokenIdentifier($raw->identifier),
                'expiration' => Timestamp::createFromString($raw->expiration),
                'revoked' => $raw->revoked,
                'traceableTime' => new TraceableTime(
                    Timestamp::createFromString($raw->created_at),
                    isset($raw->updated_at) ? Timestamp::createFromString($raw->updated_at) : null
                ),
            ])
        );
    }

    public static function toPersistence(AccessToken $accessToken): object
    {
        return (object)[
            'identifier' => strval($accessToken->getIdentifier()),
            'expiration' => strval($accessToken->getExpiration()),
            'revoked' => $accessToken->isRevoked() ? '1' : '0',
            'created_at' => strval($accessToken->getTraceableTime()->getCreatedAt()),
            'updated_at' => strval($accessToken->getTraceableTime()->getUpdatedAt()),
            'user_id' => $accessToken->getUser()->id()
        ];
    }
}
