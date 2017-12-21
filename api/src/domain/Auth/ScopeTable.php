<?php
namespace Domain\Auth;

use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;

class ScopeTable implements ScopeRepositoryInterface
{
    private $db;

    private $table;

    private $select;

    public function __construct($db)
    {
        $this->db = $db;
        $this->table = new \Zend\Db\TableGateway\TableGateway('oauth_scopes', $this->db);
    }

    public function getScopeEntityByIdentifier($identifier)
    {
        $result = $this->table->select(['identifier' => $identifier]);
        if ($result) {
            return new Scope($this->db, $result->current());
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
