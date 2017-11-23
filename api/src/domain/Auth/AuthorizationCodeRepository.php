<?php
namespace Domain\Auth;

use Analogue\ORM\Repository;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;

class AuthorizationCodeRepository extends Repository implements AuthCodeRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(AuthorizationCode::class);
    }

    public function getNewAuthCode()
    {
        return new AuthorizationCode();
    }

    public function persistNewAuthToken(AuthCodeEntityInterface $authCodeEntity)
    {
        $this->store($authCodeEntity);
    }

    public function revokeAuthCode($tokenId)
    {
        $token = $this->find($tokenId);
        if ($token) {
            $token->revoked = true;
            $this->store($token);
        }
    }

    public function isAccessTokenRevoked($tokenId)
    {
        $token = $this->find($tokenId);
        if ($token) {
            return $token->revoked;
        }
        return false;
    }
}
