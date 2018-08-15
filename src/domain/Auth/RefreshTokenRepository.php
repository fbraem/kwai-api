<?php
namespace Domain\Auth;

use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshToken)
    {
        RefreshTokensTable::getTableFromRegistry()->save($refreshToken);
    }

    public function revokeRefreshToken($tokenId)
    {
        $refreshTokenTable = RefreshTokensTable::getTableFromRegistry();
        $token = $refreshTokenTable
            ->find()
            ->where(['identifier' => $tokenId])
            ->first();
        if ($token) {
            $token->revoke();
            $refreshTokenTable->save($token);
        } else {
            throw OAuthServerException::invalidRefreshToken("RefreshToken doesn't exist");
        }
    }

    public function isRefreshTokenRevoked($tokenId)
    {
        $refreshTokenTable = RefreshTokensTable::getTableFromRegistry();
        $token = $refreshTokenTable
            ->find()
            ->where(['identifier' => $tokenId])
            ->first();
        if ($token) {
            return $token->revoked;
        }
        return false;
    }

    public function getNewRefreshToken()
    {
        return RefreshTokensTable::getTableFromRegistry()->newEntity();
    }
}
