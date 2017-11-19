<?php
namespace Domain\Auth;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;

trait TokenTrait
{
    public function getClient()
    {
        return $this->client;
    }

    public function setClient(ClientEntityInterface $client)
    {
        $this->client = $client;
    }

    public function getExpiryDatetime()
    {
        return $this->expiration;
    }

    public function setExpiryDatetime(\DateTime $datetime)
    {
        $this->expiration = $datetime;
    }

    public function getUserIdentifier()
    {
        return $this->user->id;
    }

    public function setUserIdentifier($identifier)
    {
        $repo = new UserRepository();
        $this->user = $repo->find($identifier);
    }

    public function getScopes()
    {
        return $this->scopes;
    }

    public function addScope(ScopeEntityInterface $scope)
    {
        $this->scopes->push($scope);
    }
}
