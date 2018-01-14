<?php
namespace Domain\Auth;

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class ClientTable implements ClientRepositoryInterface
{
    private $db;

    private $table;

    public function __construct($db)
    {
        $this->db = $db;
        $this->table = new \Zend\Db\TableGateway\TableGateway('oauth_clients', $this->db);
    }

    public function getClientEntity($clientIdentifier, $grantType, $clientSecrect = null, $mustValidateSecret = true)
    {
        $result = $this->table->select(['identifier' => $clientIdentifier]);
        if ($result->count() > 0) {
            return new Client($this->db, $result->current());
        }
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
