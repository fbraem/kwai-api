<?php
namespace Domain\Auth;

use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;

class AuthorizationCodeRepository implements AuthCodeRepositoryInterface
{
    public function getNewAuthCode()
    {
        return AuthorizationCodesTable::getTableFromRegistry()->newEntity();
    }

    public function persistNewAuthToken(AuthCodeEntityInterface $authCodeEntity)
    {
        AuthorizationCodesTable::getTableFromRegistry()->save($authCodeEntity);
    }

    public function revokeAuthCode($tokenId)
    {
        $table = AuthorizationCodesTable::getTableFromRegistry();
        try {
            $authCode = $table->get($tokenId);
            $authCode->revoke();
            $table->save($authCode);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
        }
    }

    public function isAccessTokenRevoked($tokenId)
    {
        $table = AuthorizationCodesTable::getTableFromRegistry();
        try {
            $authCode = $table->get($tokenId);
            return $authCode->revoked;
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $rnfe) {
        }
        return false;
    }
}
