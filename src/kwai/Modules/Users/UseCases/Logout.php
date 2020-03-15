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
use Kwai\Modules\Users\Repositories\AccessTokenRepository;
use Kwai\Modules\Users\Repositories\RefreshTokenRepository;

use Firebase\JWT\JWT;

/**
 * Usecase: Logout a user. The refreshtoken and the associated accesstoken
 * will be revoked.
 */
final class Logout
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
     * The refreshtoken and the associated accesstoken will be revoked.
     * @param  LogoutCommand $command
     * @throws \Kwai\Core\Domain\Exceptions\NotFoundException
     *    Thrown when the refreshtoken can't be found
     */
    public function __invoke(LogoutCommand $command): void
    {
        $refreshToken = $this
            ->refreshTokenRepo
            ->getByTokenIdentifier(
                new TokenIdentifier($command->identifier)
            )
        ;

        // Revoke the old refreshtoken
        $refreshToken->getAccessToken()->revoke();
        $refreshToken->revoke();
        $this->refreshTokenRepo->update($refreshToken);

        // Revoke the old accesstoken
        $accessToken = $refreshToken->getAccessToken();
        $accessToken->revoke();
        $this->accessTokenRepo->update($accessToken);
    }
}
