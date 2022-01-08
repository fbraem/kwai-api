<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Mappers;

use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Domain\Entity;
use Kwai\Modules\Users\Domain\RefreshToken;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Infrastructure\RefreshTokenTable;

/**
 * Mapper for RefreshToken entity.
 */
final class RefreshTokenDTO
{
    public function __construct(
        public RefreshTokenTable $refreshToken = new RefreshTokenTable(),
        public AccessTokenDTO    $accessTokenDTO = new AccessTokenDTO(),
    ) {
    }

    /**
     * Maps the RefreshToken table to the RefreshToken entity
     * @return RefreshToken
     */
    public function toDomain(): RefreshToken
    {
        return new RefreshToken(
            identifier: new TokenIdentifier($this->refreshToken->identifier),
            expiration: Timestamp::createFromString($this->refreshToken->expiration),
            revoked: $this->refreshToken->revoked === 1,
            traceableTime: new TraceableTime(
                Timestamp::createFromString($this->refreshToken->created_at),
                $this->refreshToken->updated_at
                    ? Timestamp::createFromString($this->refreshToken->updated_at)
                    : null
            ),
            accessToken: new Entity(
                $this->refreshToken->access_token_id,
                $this->accessTokenDTO->toDomain()
            )
        );
    }

    /**
     * Maps the RefreshToken entity to the RefreshToken table.
     *
     * @param Entity<RefreshToken>|RefreshToken $refreshToken
     * @return RefreshTokenDTO The refreshtoken table data.
     */
    public function toPersistence(Entity|RefreshToken $refreshToken): self
    {
        if (is_a($refreshToken, Entity::class)) {
            $this->refreshToken->id = $refreshToken->id();
        }
        $this->refreshToken->identifier = (string) $refreshToken->getIdentifier();
        $this->refreshToken->expiration = (string) $refreshToken->getExpiration();
        $this->refreshToken->revoked = $refreshToken->isRevoked() ? 1 : 0;
        $this->refreshToken->created_at = (string) $refreshToken->getTraceableTime()->getCreatedAt();
        if ($refreshToken->getTraceableTime()->isUpdated()) {
            $this->refreshToken->updated_at = (string)$refreshToken->getTraceableTime()->getUpdatedAt();
        }
        $this->refreshToken->access_token_id = $refreshToken->getAccessToken()->id();

        $this->accessTokenDTO->toPersistence($refreshToken->getAccessToken());

        return $this;
    }
}
