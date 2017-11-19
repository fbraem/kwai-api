<?php
namespace Domain\Auth;

use Analogue\ORM\Entity;
use Analogue\ORM\EntityCollection;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\EntityInterface;

class AccessToken extends Entity implements AccessTokenEntityInterface
{
    public function __construct()
    {
        $this->scopes = new EntityCollection();
    }

    use AccessTokenTrait;
    use IdentifierTrait;
    use TokenTrait;
}
