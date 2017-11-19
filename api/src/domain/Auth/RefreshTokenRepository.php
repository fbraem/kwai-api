<?php
namespace Domain\Auth;

use Analogue\ORM\Repository;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;

class RefreshTokenRepository extends Repository implements RefreshTokenRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(RefreshToken::class);
    }

    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshToken)
    {
        $this->store($refreshToken);
    }

    public function revokeRefreshToken($tokenId)
    {
        // Some logic to revoke the refresh token in a database
    }

    public function isRefreshTokenRevoked($tokenId)
    {
        return false; // The refresh token has not been revoked
    }

    public function getNewRefreshToken()
    {
        return new RefreshToken();
    }
}
