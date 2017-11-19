<?php
namespace Domain\User;

use Analogue\ORM\Entity;
use League\OAuth2\Server\Entities\UserEntityInterface;

/**
 * @inheritdoc
 */
class User extends Entity implements UserEntityInterface
{
    protected $hidden = ['password'];

    public function __construct($email)
    {
        $this->email = $email;
    }

    public function getIdentifier()
    {
        return $this->id;
    }
}
