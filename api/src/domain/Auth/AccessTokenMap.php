<?php
namespace Domain\Auth;

use Analogue\ORM\EntityMap;

/**
 * @inheritdoc
 */
class AccessTokenMap extends EntityMap
{
    protected $table = 'oauth_access_tokens';

    public $timestamps = true;

    public function client(AccessToken $authToken)
    {
        return $this->belongsTo($authToken, Client::class, 'client_id', 'id');
    }

    public function user(AccessToken $authToken)
    {
        return $this->belongsTo($authToken, \Domain\User\User::class, 'user_id', 'id');
    }

    public function scopes(AccessToken $authToken)
    {
        return $this->belongsToMany($authToken, Scope::class, 'oauth_access_token_scopes', 'access_token_id', 'scope_id');
    }
}
