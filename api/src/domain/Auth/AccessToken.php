<?php
namespace Domain\Auth;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\EntityInterface;

class AccessToken extends \Cake\ORM\Entity implements AccessTokenEntityInterface
{
    use AccessTokenTrait;
    use IdentifierTrait;
    use TokenTrait;

    use \Domain\DatetimeMetaTrait;

    public function revoke()
    {
        $this->revoked = true;
    }
}
