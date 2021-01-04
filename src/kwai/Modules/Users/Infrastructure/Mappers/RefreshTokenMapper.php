<?php
/**
 * @package kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Mappers;

use Illuminate\Support\Collection;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\Entity;
use Kwai\Modules\Users\Domain\RefreshToken;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;

/**
 * Mapper for RefreshToken entity.
 */
final class RefreshTokenMapper
{
    /**
     * Maps the RefreshToken table to the RefreshToken entity
     *
     * @param Collection $data
     * @return RefreshToken
     */
    public static function toDomain(Collection $data): RefreshToken
    {
        $accessToken = $data->get('accessToken');

        return new RefreshToken(
            identifier: new TokenIdentifier($data->get('identifier')),
            expiration: Timestamp::createFromString($data->get('expiration')),
            revoked: $data->get('revoked', '0') === '1',
            traceableTime: new TraceableTime(
                Timestamp::createFromString($data->get('created_at')),
                $data->has('updated_at') ? Timestamp::createFromString($data->get('updated_at')) : null
            ),
            accessToken: new Entity(
                $accessToken->get('id'),
                AccessTokenMapper::toDomain($$accessToken)
            )
        );
    }

    /**
     * Maps the RefreshToken entity to the RefreshToken table.
     *
     * @param RefreshToken $refreshToken
     * @return Collection The raw table data.
     */
    public static function toPersistence(RefreshToken $refreshToken): Collection
    {
        return collect([
            'identifier' => strval($refreshToken->getIdentifier()),
            'expiration' => strval($refreshToken->getExpiration()),
            'revoked' => $refreshToken->isRevoked() ? '1' : '0',
            'created_at' => strval($refreshToken->getTraceableTime()->getCreatedAt()),
            'updated_at' => $refreshToken->getTraceableTime()->getUpdatedAt()?->__toString(),
            'access_token_id' => $refreshToken->getAccessToken()->id()
        ]);
    }
}
