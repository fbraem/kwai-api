<?php
namespace Domain\Auth;

use Analogue\ORM\Repository;

use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

class AccessTokenRepository extends Repository implements AccessTokenRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(AccessToken::class);
    }

    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $accessToken = new AccessToken();
        $accessToken->client = $clientEntity;

        /* Ignore scopes here: see https://github.com/thephpleague/oauth2-server/issues/728
        foreach($scopes as $scope)
        {
            $accessToken->scopes->push($scope);
        }
        */
        if ($userIdentifier) {
            $accessToken->setUserIdentifier($userIdentifier);
        }
        return $accessToken;
    }

    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity)
    {
        $this->store($accessTokenEntity);
    }

    public function revokeAccessToken($tokenId)
    {
        //TODO:
    }

    public function isAccessTokenRevoked($tokenId)
    {
        //TODO:
        return false;
    }
}
