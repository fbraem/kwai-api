<?php
namespace Domain\Auth;

use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $accessToken = AccessTokensTable::getTableFromRegistry()->newEntity();
        $accessToken->client = $clientEntity;

        $accessToken->scopes = [];
        foreach ($scopes as $scope) {
            $accessToken->scopes[$scope->getIdentifier()] = $scope;
        }

        if ($userIdentifier) {
            $accessToken->setUserIdentifier($userIdentifier);
        }
        return $accessToken;
    }

    public function persistNewAccessToken(AccessTokenEntityInterface $accessToken)
    {
        AccessTokensTable::getTableFromRegistry()->save($accessToken);
    }

    public function revokeAccessToken($tokenId)
    {
        $accessTokenTable = AccessTokensTable::getTableFromRegistry();
        $accessToken = $accessTokenTable
            ->find()
            ->where(['identifier' => $tokenId])
            ->first()
        ;
        if ($accessToken) {
            $accessToken->revoke();
            $accessTokenTable->save($accessToken);
        }
    }

    public function isAccessTokenRevoked($tokenId)
    {
        $accessTokenTable = AccessTokensTable::getTableFromRegistry();
        $accessToken = $accessTokenTable
            ->find()
            ->where(['identifier' => $tokenId])
            ->first()
        ;
        if ($accessToken) {
            return $accessToken->revoked;
        }
        return true;
    }
}
