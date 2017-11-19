<?php
namespace Domain\Auth;

use Analogue\ORM\Repository;
use League\OAuth2\Server\Repositories\AuthorizationCodeRepositoryInterface;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;

class AuthorizationCodeRepository extends Repository implements AuthorizationCodeRepositoryInterface
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

    public function revokeAccessToken($tokenId)
    {
        // Some logic to revoke the auth code in a database
    }

    public function isAccessTokenRevoked($tokenId)
    {
        //TODO:
        return false; // The auth code has not been revoked
    }
}
