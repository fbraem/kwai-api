<?php
namespace Domain\Auth;

use League\OAuth2\Server\Entities\ClientEntityInterface;

/**
 * @inheritdoc
 */
class Client extends \Cake\ORM\Entity implements ClientEntityInterface
{
    use IdentifierTrait;
    use \Domain\DatetimeMetaTrait;

    public function getName()
    {
        return $this->name;
    }

    public function getRedirectUri()
    {
        return $this->redirect_uri;
    }
}
