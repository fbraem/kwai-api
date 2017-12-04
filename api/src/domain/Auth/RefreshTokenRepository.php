<?php
namespace Domain\Auth;

use Analogue\ORM\Repository;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;

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
        $token = $this->firstMatching(['identifier' => $tokenId]);
        if ($token) {
            $token->revoked = true;
            $this->store($token);
        } else {
            throw OAuthServerException::invalidRefreshToken("RefreshToken doesn't exist");
        }
    }

    public function isRefreshTokenRevoked($tokenId)
    {
        $token = $this->firstMatching(['identifier' => $tokenId]);
        if ($token) {
            return $token->revoked;
        }
        return false;
    }

    public function getNewRefreshToken()
    {
        return new RefreshToken();
    }
}
