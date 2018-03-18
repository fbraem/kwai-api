<?php
namespace Domain\Auth;

use League\OAuth2\Server\Entities\ScopeEntityInterface;

class Scope extends \Cake\ORM\Entity implements ScopeEntityInterface
{
    use IdentifierTrait;
    use \Domain\DatetimeMetaTrait;

    //TODO: do we need this?
    public function jsonSerialize()
    {
        return $this->getIdentifier();
    }
}
