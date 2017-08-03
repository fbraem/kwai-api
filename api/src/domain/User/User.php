<?php
namespace Domain\User;

use Analogue\ORM\Entity;

/**
 * @inheritdoc
 */
class User extends Entity
{
    protected $hidden = ['password'];

    public function __construct($email)
    {
        $this->email = $email;
    }
}
