<?php
/**
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
use Kwai\Modules\Users\Domain\RefreshToken;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;

/**
 * Mapper for RefreshToken entity.
 */
final class RefreshTokenMapper
{
    /**
     * Maps the RefreshToken table to the RefreshToken entity
     * @param  object $raw Row data from the table
     * @return Entity<RefreshToken>
     */
    public static function toDomain(object $raw): Entity
    {
        return new Entity(
            (int) $raw->id,
            new RefreshToken((object)[
                'identifier' => new TokenIdentifier($raw->identifier),
                'expiration' => Timestamp::createFromString($raw->expiration),
                'revoked' => $raw->revoked,
                'traceableTime' => new TraceableTime(
                    Timestamp::createFromString($raw->created_at),
                    isset($raw->updated_at) ? Timestamp::createFromString($raw->updated_at) : null
                ),
                'accessToken' => AccessTokenMapper::toDomain($raw->accessToken)
            ])
        );
    }

    public static function toPersistence(RefreshToken $refreshToken): array
    {
        if ($refreshToken->getTraceableTime()->getUpdatedAt()) {
            $updated_at = strval(
                $refreshToken->getTraceableTime()->getUpdatedAt()
            );
        } else {
            $updated_at = null;
        }
        return [
            'identifier' => strval($refreshToken->getIdentifier()),
            'expiration' => strval($refreshToken->getExpiration()),
            'revoked' => $refreshToken->isRevoked() ? '1' : '0',
            'created_at' => strval($refreshToken->getTraceableTime()->getCreatedAt()),
            'updated_at' => $updated_at,
            'access_token_id' => $refreshToken->getAccessToken()->id()
        ];
    }
}
