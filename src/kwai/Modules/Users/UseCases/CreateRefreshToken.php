<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\ValueObjects\Timestamp;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\RefreshTokenNotFoundException;
use Kwai\Modules\Users\Domain\RefreshTokenEntity;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Domain\RefreshToken;
use Kwai\Modules\Users\Repositories\AccessTokenRepository;
use Kwai\Modules\Users\Repositories\RefreshTokenRepository;
use Kwai\Modules\Users\Domain\Exceptions\AuthenticationException;

/**
 * CreateRefreshToken
 *
 * Create a new access- and refreshtoken
 */
class CreateRefreshToken
{
    /**
     * Constructor.
     * @param RefreshTokenRepository $refreshTokenRepo An refreshtoken repository
     * @param AccessTokenRepository $accessTokenRepo An accesstoken repository
     */
    public function __construct(
        private RefreshTokenRepository $refreshTokenRepo,
        private AccessTokenRepository $accessTokenRepo
    ) {
    }

    public static function create(RefreshTokenRepository $refreshTokenRepo, AccessTokenRepository $accessTokenRepo)
    {
        return new self($refreshTokenRepo, $accessTokenRepo);
    }

    /**
     * Create a new refresh- and accesstoken using the old refresh token.
     * The refreshtoken must be valid and the user is checked. On succes, a new
     * refreshtoken with a new accesstoken is returned.
     *
     * @param CreateRefreshTokenCommand $command
     * @return RefreshTokenEntity A RefreshToken entity
     * @throws AuthenticationException Thrown when the refresh token is expired.
     * @throws RefreshTokenNotFoundException
     * @throws RepositoryException
     */
    public function __invoke(CreateRefreshTokenCommand $command): RefreshTokenEntity
    {
        $refreshToken = $this
            ->refreshTokenRepo
            ->getByTokenIdentifier(
                new TokenIdentifier($command->identifier)
            )
        ;
        if ($refreshToken->isExpired()) {
            throw new AuthenticationException('Refreshtoken is expired');
        }
        if ($refreshToken->isRevoked()) {
            throw new AuthenticationException('Refreshtoken is revoked');
        }

        // Revoke the old refreshtoken
        $refreshToken->getAccessToken()->revoke();
        $refreshToken->revoke();
        $this->refreshTokenRepo->update($refreshToken);

        // Revoke the old accesstoken
        $accessToken = $refreshToken->getAccessToken();
        $accessToken->revoke();
        $this->accessTokenRepo->update($accessToken);

        $user = $refreshToken->getAccessToken()->getUserAccount();
        if ($user->isRevoked()) {
            throw new AuthenticationException('User is revoked');
        }

        $accessToken = $this->accessTokenRepo->create(
            new AccessToken(
                identifier: new TokenIdentifier(),
                expiration: Timestamp::createFromDateTime(
                    new \DateTime('now +2 hours')
                ),
                account: $user
            )
        );

        return $this->refreshTokenRepo->create(
            new RefreshToken(
                identifier: new TokenIdentifier(),
                expiration: Timestamp::createFromDateTime(
                    new \DateTime('now +1 month')
                ),
                accessToken: $accessToken
            )
        );
    }
}
