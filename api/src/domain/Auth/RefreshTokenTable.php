<?php
namespace Domain\Auth;

use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;

class RefreshTokenTable implements RefreshTokenRepositoryInterface
{
    private $db;

    private $table;

    private $select;

    public function __construct($db)
    {
        $this->db = $db;
        $this->table = new \Zend\Db\TableGateway\TableGateway('oauth_refresh_tokens', $this->db);
        $this->select = $this->table->getSql()->select();
    }

    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshToken)
    {
        $refreshToken->store($this->db);
    }

    public function revokeRefreshToken($tokenId)
    {
        $this->whereIdentifier(['identifier' => $tokenId]);
        try {
            $token = $this->findOne();
            $token->revoke();
            $token->store();
        } catch (\Domain\NotFoundException $nfe) {
            throw OAuthServerException::invalidRefreshToken("RefreshToken doesn't exist");
        }
    }

    public function isRefreshTokenRevoked($tokenId)
    {
        $this->whereIdentifier(['identifier' => $tokenId]);
        try {
            $token = $this->findOne();
            return $token->revoked();
        } catch (\Domain\NotFoundException $nfe) {
        }
        return false;
    }

    public function getNewRefreshToken()
    {
        return new RefreshToken($this->db);
    }

    public function whereId($ids)
    {
        $this->select->where(['id' => $ids]);
        return $this;
    }

    public function whereIdentifier($identifier)
    {
        $this->select->where(['identifier' => $identifier]);
        return $this;
    }

    public function findOne() : RefreshToken
    {
        $result = $this->table->selectWith($this->select);
        if ($result->count() > 0) {
            return new RefreshToken($this->db, $result->current());
        }
        throw new \Domain\NotFoundException("RefreshToken not found");
    }
}
