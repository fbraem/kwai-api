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
        return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->expiration);
    }

    public function setExpiryDatetime(\DateTime $datetime)
    {
        $this->expiration = \Carbon\Carbon::instance($datetime)->toDateTimeString();
    }

    public function getUserIdentifier()
    {
        return $this->user->id();
    }

    public function setUserIdentifier($identifier)
    {
        $users = (new \Domain\User\UsersTable($this->db))->whereId($identifier)->find();
        if (count($users) > 0) {
            $this->user = reset($users);
        }
    }

    public function getScopes()
    {
        return $this->scopes;
    }

    public function addScope(ScopeEntityInterface $scope)
    {
        if (!$this->scopes) {
            $this->scopes = [];
        }
        $this->scopes[] = $scope;
    }
}
