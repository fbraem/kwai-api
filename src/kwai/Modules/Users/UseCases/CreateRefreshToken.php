<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Entity;
use Kwai\Core\Domain\Timestamp;

use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Domain\AccessToken;
use Kwai\Modules\Users\Domain\RefreshToken;
use Kwai\Modules\Users\Repositories\UserRepository;
use Kwai\Modules\Users\Repositories\AccessTokenRepository;
use Kwai\Modules\Users\Repositories\RefreshTokenRepository;
use Kwai\Modules\Users\Domain\Exceptions\AuthenticationException;

use Firebase\JWT\JWT;

/**
 * Usecase: Create a new access- and refreshtoken
 */
final class CreateRefreshToken
{
    /**
     * @var AccessTokenRepository
     */
    private $accessTokenRepo;

    /**
     * @var RefreshTokenRepository
     */
    private $refreshTokenRepo;

    /**
     * Constructor.
     * @param RefreshTokenRepository $refreshTokenRepo An refreshtoken repository
     * @param AccessTokenRepository $accessTokenRepo An accesstoken repository
     */
    public function __construct(
        RefreshTokenRepository $refreshTokenRepo,
        AccessTokenRepository $accessTokenRepo
    ) {
        $this->refreshTokenRepo = $refreshTokenRepo;
        $this->accessTokenRepo = $accessTokenRepo;
    }

    /**
     * Create a new refresh- and accesstoken using the old refresh token.
     * The refreshtoken must be valid and the user is checked. On succes, a new
     * refreshtoken with a new accesstoken is returned.
     * @param  CreateRefreshTokenCommand $command
     * @return Entity<RefreshToken>               A RefreshToken entity
     * @throws \Kwai\Core\Domain\Exceptions\NotFoundException
     *    Thrown when the refreshtoken can't be found
     * @throws AuthenticationException
     *    Thrown when the refresh token is expired.
     */
    public function __invoke(CreateRefreshTokenCommand $command): Entity
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
            new AccessToken((object) [
                'identifier' => new TokenIdentifier(),
                'expiration' => Timestamp::createFromDateTime(
                    new \DateTime('now +2 hours')
                ),
                'account' => $user
            ])
        );

        $refreshToken = $this->refreshTokenRepo->create(
            new RefreshToken((object)[
                'identifier' => new TokenIdentifier(),
                'expiration' => Timestamp::createFromDateTime(
                    new \DateTime('now +1 month')
                ),
                'accessToken' => $accessToken
            ])
        );

        return $refreshToken;
    }
}
