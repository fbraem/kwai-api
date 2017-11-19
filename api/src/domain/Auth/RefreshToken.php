<?php
namespace Domain\Auth;

use Analogue\ORM\Entity;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;

/**
 * @inheritdoc
 */
class RefreshToken extends Entity implements RefreshTokenEntityInterface
{
    use IdentifierTrait;

    public function __construct()
    {
    }

    public function setAccessToken(AccessTokenEntityInterface $accessToken)
    {
        $this->attributes['accesstoken'] = $accessToken;
    }

    public function getAccessToken()
    {
        return $this->attributes['accesstoken'];
    }

    public function getExpiryDateTime()
    {
        return $this->expiration;
    }

    public function setExpiryDateTime(\DateTime $dateTime)
    {
        $this->expiration = $dateTime;
    }
}
