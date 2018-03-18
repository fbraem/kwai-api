<?php
namespace Domain\Auth;

use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;

/**
 * @inheritdoc
 */
class RefreshToken extends \Cake\ORM\Entity implements RefreshTokenEntityInterface
{
    use IdentifierTrait;
    use \Domain\DatetimeMetaTrait;

    public function setAccessToken(AccessTokenEntityInterface $accessToken)
    {
        $this->accesstoken = $accessToken;
    }

    public function getAccessToken()
    {
        return $this->accesstoken;
    }

    public function getExpiryDateTime()
    {
        return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->expiration);
    }

    public function setExpiryDateTime(\DateTime $datetime)
    {
        $this->expiration = \Carbon\Carbon::instance($datetime)->toDateTimeString();
    }

    public function revoke()
    {
        $this->revoked = true;
    }
}
