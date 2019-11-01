<?php
namespace Domain\Auth;

trait IdentifierTrait
{
    // The following functions are required for OAuth2 League
    public function getIdentifier()
    {
        return $this->identifier;
    }

    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    public function isConfidential()
    {
        return true;
    }
}
