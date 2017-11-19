<?php
namespace Domain\Auth;

use Analogue\ORM\Repository;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class ClientRepository extends Repository implements ClientRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(Client::class);
    }

    public function getClientEntity($clientIdentifier, $grantType, $clientSecrect = null, $mustValidateSecret = true)
    {
        $client = $this->mapper->where('identifier', '=', $clientIdentifier)->first();
/*
        if (
            $mustValidateSecret === true
            && $clients[$clientIdentifier]['is_confidential'] === true
            && password_verify($clientSecret, $clients[$clientIdentifier]['secret']) === false
        ) {
            return;
        }
*/

        return $client;
    }
}
