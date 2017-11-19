<?php
namespace Domain\Auth;

use Analogue\ORM\EntityMap;

/**
 * @inheritdoc
 */
class RefreshTokenMap extends EntityMap
{
    protected $table = 'oauth_refresh_tokens';

    public $timestamps = true;

    public function accesstoken(RefreshToken $token)
    {
        return $this->belongsTo($token, AccessToken::class, 'access_token_id', 'id');
    }
}
