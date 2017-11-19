<?php
namespace Domain\Auth;

use Analogue\ORM\Entity;

use League\OAuth2\Server\Entities\ScopeEntityInterface;

/**
 * @inheritdoc
 */
class Scope extends Entity implements ScopeEntityInterface
{
    use IdentifierTrait;

    public function __construct()
    {
    }

    //TODO: do we need this?
    public function jsonSerialize()
    {
        return $this->getIdentifier();
    }
}
