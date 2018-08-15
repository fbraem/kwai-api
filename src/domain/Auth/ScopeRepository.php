<?php
namespace Domain\Auth;

use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;

class ScopeRepository implements ScopeRepositoryInterface
{
    public function getScopeEntityByIdentifier($identifier)
    {
        $scope = ScopesTable::getTableFromRegistry()
            ->find()
            ->where(['identifier' => $identifier])
            ->first()
        ;
        if ($scope) {
            return $scope;
        }

        throw OAuthServerException::invalidRefreshToken("Scope doesn't exist");
    }

    public function finalizeScopes(array $scopes, $grantType, ClientEntityInterface $client, $userIdentifier = null)
    {
        return $scopes;
        //TODO:
        // Example of programatically modifying the final scope of the access token
/*
        if ((int) $userIdentifier === 1) {
            $scope = new ScopeEntity();
            $scope->setIdentifier('email');
            $scopes[] = $scope;
        }
*/
        //return $scopes;
    }
}
