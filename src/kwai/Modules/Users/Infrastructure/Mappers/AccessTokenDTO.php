<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\Infrastructure\Mappers;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\ValueObjects\TraceableTime;
use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Infrastructure\AccessTokenTable;
use Kwai\Modules\Users\Infrastructure\UsersTableSchema;

final class AccessTokenDTO
{
    public function __construct(
        public AccessTokenTable $accessToken = new AccessTokenTable(),
        public UserAccountDTO $userAccount = new UserAccountDTO()
    ) {
    }

    public function create(): AccessToken
    {
        return new AccessToken(
            identifier: new TokenIdentifier($this->accessToken->identifier),
            expiration: Timestamp::createFromString($this->accessToken->expiration),
            revoked: $this->accessToken->revoked === 1,
            traceableTime: new TraceableTime(
                Timestamp::createFromString($this->accessToken->created_at),
                $this->accessToken->updated_at
                    ? Timestamp::createFromString($this->accessToken->updated_at)
                    : null
            ),
            account: new Entity(
                (int) $this->userTable->id,
                UserAccountDTO::toDomain($this->userTable)
            )
        );
    }

    /**
     * Persist an AccessToken domain object to a database row.
     *
     * @param AccessToken $accessToken
     * @return $this
     */
    public function persist(AccessToken $accessToken): static
    {
        $this->accessToken->identifier = (string) $accessToken->getIdentifier();
        $this->accessToken->expiration = (string) $accessToken->getExpiration();
        $this->accessToken->revoked = $accessToken->isRevoked() ? 1 : 0;
        $this->accessToken->created_at = (string) $accessToken->getTraceableTime()->getCreatedAt();
        if ($accessToken->getTraceableTime()->isUpdated()) {
            $this->accessToken->updated_at = (string)$accessToken->getTraceableTime()->getUpdatedAt();
        }
        $this->accessToken->user_id = $accessToken->getUserAccount()->id();
        return $this;
    }

    /**
     * Persist an AccessToken entity to a database row.
     *
     * @param Entity<AccessToken> $accessToken
     * @return $this
     */
    public function persistEntity(Entity $accessToken): static
    {
        $this->accessToken->id = $accessToken->id();
        return $this->persist($accessToken->domain());
    }
}
