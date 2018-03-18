<?php
namespace Domain\Auth;

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class ClientRepository implements ClientRepositoryInterface
{
    public function getClientEntity($clientIdentifier, $grantType = null, $clientSecrect = null, $mustValidateSecret = true)
    {
        return ClientsTable::getTableFromRegistry()
            ->find()
            ->where(['identifier' => $clientIdentifier])
            ->first()
        ;
        /*
                if (
                    $mustValidateSecret === true
                    && $clients[$clientIdentifier]['is_confidential'] === true
                    && password_verify($clientSecret, $clients[$clientIdentifier]['secret']) === false
                ) {
                    return;
                }
        */

        //return $client;
    }
}
