<?php

namespace Domain\User;

use Analogue\ORM\Repository;

/**
 * Repository that handles read/writes for User
 */
class UserRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(User::class);
    }

    public function count()
    {
        return $this->mapper->count();
    }

    public function findWithEmail($email)
    {
        return $this->firstMatching(['email' => $email]);
    }
}
