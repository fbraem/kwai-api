<?php
namespace Domain\Auth;

use League\OAuth2\Server\Entities\AuthCodeEntityInterface;

/**
 * @inheritdoc
 */
class AuthorizationCode extends \Cake\ORM\Entity implements AuthCodeEntityInterface
{
    use TokenTrait;
    use IdentifierTrait;
    use \Domain\DatetimeMetaTrait;

    public function getRedirectUri()
    {
        return $this->redirect_uri;
    }
}
