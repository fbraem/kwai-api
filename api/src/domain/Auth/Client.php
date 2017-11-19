<?php
namespace Domain\Auth;

use Analogue\ORM\Entity;

use League\OAuth2\Server\Entities\ClientEntityInterface;

/**
 * @inheritdoc
 */
class Client extends Entity implements ClientEntityInterface
{
    use IdentifierTrait;

    public function __construct()
    {
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
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
