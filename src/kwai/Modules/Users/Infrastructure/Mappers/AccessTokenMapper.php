<?php
/**
 * Mapper for AccessToken entity.
 * @package kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Mappers;

use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\Timestamp;
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
                'revoked' => ($raw->revoked ?? '0') == '1',
                'traceableTime' => new TraceableTime(
                    Timestamp::createFromString($raw->created_at),
                    isset($raw->updated_at) ? Timestamp::createFromString($raw->updated_at) : null
                ),
                'account' => UserAccountMapper::toDomain($raw->user)
            ])
        );
    }

    public static function toPersistence(AccessToken $accessToken): array
    {
        if ($accessToken->getTraceableTime()->getUpdatedAt()) {
            $updated_at = strval(
                $accessToken->getTraceableTime()->getUpdatedAt()
            );
        } else {
            $updated_at = null;
        }
        return [
            'identifier' => strval($accessToken->getIdentifier()),
            'expiration' => strval($accessToken->getExpiration()),
            'revoked' => $accessToken->isRevoked() ? '1' : '0',
            'created_at' => strval($accessToken->getTraceableTime()->getCreatedAt()),
            'updated_at' => $updated_at,
            'user_id' => $accessToken->getUserAccount()->id()
        ];
    }
}
