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
        return $this->user->id;
    }

    public function setUserIdentifier($identifier)
    {
        try {
            $this->user = \Domain\User\UsersTable::getTableFromRegistry()->get($identifier);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
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
