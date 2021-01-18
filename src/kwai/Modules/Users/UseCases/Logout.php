<?php
/**
 * @package Modules
 * @subpackage Users
 */
declare(strict_types = 1);

namespace Kwai\Modules\Users\UseCases;

use Kwai\Core\Infrastructure\Repositories\RepositoryException;
use Kwai\Modules\Users\Domain\Exceptions\RefreshTokenNotFoundException;
use Kwai\Modules\Users\Domain\ValueObjects\TokenIdentifier;
use Kwai\Modules\Users\Repositories\AccessTokenRepository;
use Kwai\Modules\Users\Repositories\RefreshTokenRepository;

/**
 * Usecase: Logout a user. The refreshtoken and the associated accesstoken
 * will be revoked.
 */
final class Logout
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

    /**
     * Factory method
     *
     * @param RefreshTokenRepository $refreshTokenRepo
     * @param AccessTokenRepository  $accessTokenRepo
     * @return Logout
     */
    public static function create(
        RefreshTokenRepository $refreshTokenRepo,
        AccessTokenRepository $accessTokenRepo
    ): self {
        return new self($refreshTokenRepo, $accessTokenRepo);
    }

    /**
     * The refreshtoken and the associated accesstoken will be revoked.
     *
     * @param LogoutCommand $command
     * @throws RepositoryException
     * @throws RefreshTokenNotFoundException
     * @noinspection PhpUndefinedMethodInspection
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
        $refreshToken->revoke();
        $this->refreshTokenRepo->update($refreshToken);

        // Revoke the old accesstoken
        $accessToken = $refreshToken->getAccessToken();
        $accessToken->revoke();
        $this->accessTokenRepo->update($accessToken);
    }
}
