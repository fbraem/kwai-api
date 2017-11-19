<?php
namespace Domain\Auth;

use Analogue\ORM\Entity;
use Analogue\ORM\EntityCollection;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;

/**
 * @inheritdoc
 */
class AuthorizationCode extends Entity implements AuthCodeEntityInterface
{
    use TokenTrait;
    use IdentifierTrait;

    public function __construct()
    {
        $this->scopes = new EntityCollection();
    }

    public function getRedirectUri()
    {
        return $this->redirect_uri;
    }

    public function setRedirectUri($uri)
    {
        $this->redirect_uri = $uri;
    }
}
