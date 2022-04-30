<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Mappers;

use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Modules\Users\Domain\RefreshToken;
use Kwai\Modules\Users\Domain\RefreshTokenEntity;
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
     * Creates a RefreshToken domain object from a database row.
     *
     * @return RefreshToken
     */
    public function create(): RefreshToken
    {
        return new RefreshToken(
            identifier: new TokenIdentifier($this->refreshToken->identifier),
            expiration: Timestamp::createFromString($this->refreshToken->expiration),
            accessToken: $this->accessTokenDTO->createEntity(),
            revoked: $this->refreshToken->revoked === 1,
            traceableTime: new TraceableTime(
                Timestamp::createFromString($this->refreshToken->created_at),
                $this->refreshToken->updated_at
                    ? Timestamp::createFromString($this->refreshToken->updated_at)
                    : null
            )
        );
    }

    /**
     * Creates a RefreshToken entity from a database row.
     *
     * @return RefreshTokenEntity
     */
    public function createEntity(): RefreshTokenEntity
    {
        return new RefreshTokenEntity(
            $this->refreshToken->id,
            $this->create()
        );
    }

    /**
     * Persists a RefreshToken domain object to a database row.
     *
     * @param RefreshToken $refreshToken
     * @return RefreshTokenDTO
     */
    public function persist(RefreshToken $refreshToken): RefreshTokenDTO
    {
        $this->refreshToken->identifier = (string) $refreshToken->getIdentifier();
        $this->refreshToken->expiration = (string) $refreshToken->getExpiration();
        $this->refreshToken->revoked = $refreshToken->isRevoked() ? 1 : 0;
        $this->refreshToken->created_at = (string) $refreshToken->getTraceableTime()->getCreatedAt();
        if ($refreshToken->getTraceableTime()->isUpdated()) {
            $this->refreshToken->updated_at = (string)$refreshToken->getTraceableTime()->getUpdatedAt();
        }
        $this->refreshToken->access_token_id = $refreshToken->getAccessToken()->id();
        return $this;
    }

    /**
     * Persists a RefreshToken entity to a database row.
     *
     * @param RefreshTokenEntity $refreshToken
     * @return $this
     */
    public function persistEntity(RefreshTokenEntity $refreshToken): RefreshTokenDTO
    {
        $this->refreshToken->id = $refreshToken->id();
        return $this->persist($refreshToken->domain());
    }
}
