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

    private $select;

    public function __construct($db)
    {
        $this->db = $db;
        $this->table = new \Zend\Db\TableGateway\TableGateway('oauth_access_tokens', $this->db);
        $this->select = $this->table->getSql()->select();
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
        try {
            $this->whereIdentifier($tokenId);
            $accessToken = $this->findOne();
            $accessToken->revoke();
            $accessToken->store();
        } catch (\Domain\NotFoundException $nfe) {
        }
    }

    public function isAccessTokenRevoked($tokenId)
    {
        try {
            $this->whereIdentifier($tokenId);
            $accessToken = $this->findOne();
            return $accessToken->revoked();
        } catch (\Domain\NotFoundException $nfe) {
            return true;
        }
    }

    public function whereId($ids)
    {
        $this->select->where(['id' => $ids]);
        return $this;
    }

    public function whereIdentifier($identifier)
    {
        $this->select->where(['identifier' => $identifier]);
        return $this;
    }

    public function findOne() : AccessToken
    {
        $result = $this->table->selectWith($this->select);
        if ($result->count() > 0) {
            return new AccessToken($this->db, $result->current());
        }
        throw new \Domain\NotFoundException("AccessToken not found");
    }
}
