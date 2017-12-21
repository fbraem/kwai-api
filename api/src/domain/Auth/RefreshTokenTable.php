<?php
namespace Domain\Auth;

use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;

class RefreshTokenTable implements RefreshTokenRepositoryInterface
{
    private $db;

    private $table;

    public function __construct($db)
    {
        $this->db = $db;
        $this->table = new \Zend\Db\TableGateway\TableGateway('oauth_refresh_tokens', $this->db);
    }

    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshToken)
    {
        $refreshToken->store($this->db);
    }

    public function revokeRefreshToken($tokenId)
    {
        $result = $this->table->select(['identifier' => $tokenId]);
        if ($result->count() > 0) {
            $token = new RefreshToken($this->db, $result->current());
            $token->revoke();
            $token->store();
        } else {
            throw OAuthServerException::invalidRefreshToken("RefreshToken doesn't exist");
        }
    }

    public function isRefreshTokenRevoked($tokenId)
    {
        $result = $this->table->select(['identifier' => $tokenId]);
        if ($result) {
            $token = new RefreshToken($this->db, $result->current());
            if ($token) {
                return $token->revoked();
            }
        }
        return false;
    }

    public function getNewRefreshToken()
    {
        return new RefreshToken($this->db);
    }
}
