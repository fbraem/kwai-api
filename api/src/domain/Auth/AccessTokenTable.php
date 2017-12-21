<?php
namespace Domain\Auth;

use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

class AccessTokenTable implements AccessTokenRepositoryInterface
{
    private $db;

    private $table;

    public function __construct($db)
    {
        $this->db = $db;
        $this->table = new \Zend\Db\TableGateway\TableGateway('oauth_access_tokens', $this->db);
    }

    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        $accessToken = new AccessToken($this->db, [
            'client' => $clientEntity
        ]);

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

    public function persistNewAccessToken(AccessTokenEntityInterface $accessToken)
    {
        $accessToken->store();
    }

    public function revokeAccessToken($tokenId)
    {
        $result = $this->table->select(['identifier' => $tokenId]);
        if ($result->count() > 0) {
            $token = new AccessToken($this->db, $result->current());
            $token->revoke();
            $token->store();
        }
    }

    public function isAccessTokenRevoked($tokenId)
    {
        $result = $this->table->select(['identifier' => $tokenId]);
        if ($result->count() > 0) {
            $token = new AccessToken($this->db, $result->current());
            return $token->revoked();
        }
        return true;
    }
}
