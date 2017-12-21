<?php
namespace Domain\Auth;

use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;

class AuthorizationCodeTable implements AuthCodeRepositoryInterface
{
    private $db;

    private $table;

    public function __construct($db)
    {
        $this->db = $db;
        $this->table = new \Zend\Db\TableGateway\TableGateway('oauth_auth_codes', $this->db);
    }

    public function getNewAuthCode()
    {
        return new AuthorizationCode($this->db);
    }

    public function persistNewAuthToken(AuthCodeEntityInterface $authCodeEntity)
    {
        $authCodeEntity->store();
    }

    public function revokeAuthCode($tokenId)
    {
        $authCode = $this->find($tokenId);
        if ($authCode) {
            $authCode->revoke();
            $authCode->store();
        }
    }

    public function isAccessTokenRevoked($tokenId)
    {
        $authCode = $this->find($tokenId);
        if ($authCode) {
            return $authCode->revoked();
        }
        return false;
    }

    private function find($id)
    {
        $result = $this->table->select(['id' => $tokenId]);
        if ($result) {
            return new AuthorizationCode($this->db, $result->current());
        }
        return null;
    }
}
