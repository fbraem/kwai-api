<?php
namespace Domain\Auth;

use Analogue\ORM\Repository;
use Analogue\ORM\EntityCollection;

use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;

class ScopeRepository extends Repository implements ScopeRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(Scope::class);
    }

    public function getScopeEntityByIdentifier($identifier)
    {
        $scope = $this->mapper->where('identifier', '=', $identifier)->first();
        return $scope;
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
