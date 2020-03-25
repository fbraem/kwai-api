<?php
/**
 * @package Kwai
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Domain\Exceptions\NotFoundException;
use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Repositories\AccessTokenRepository;
use Kwai\Modules\Users\Repositories\RefreshTokenRepository;

/**
 * Usecase: Logout a user. The refreshtoken and the associated accesstoken
 * will be revoked.
 */
final class Logout
{
    private AccessTokenRepository $accessTokenRepo;

    private RefreshTokenRepository $refreshTokenRepo;

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
     *
     * @param  LogoutCommand $command
     * @throws NotFoundException
     *    Thrown when the refreshtoken can't be found
     * @throws RepositoryException
     * @noinspection PhpUndefinedMethodInspection*/
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
