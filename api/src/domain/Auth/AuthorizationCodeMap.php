<?php
namespace Domain\Auth;

use Analogue\ORM\EntityMap;

/**
 * @inheritdoc
 */
class AuthorizationCodeMap extends EntityMap
{
    protected $table = 'oauth_auth_codes';

    public $timestamps = true;
}
