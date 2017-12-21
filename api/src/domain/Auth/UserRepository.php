<?php
namespace Domain\Auth;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function getUserEntityByUserCredentials($email, $password, $grantType, ClientEntityInterface $client)
    {
        $users = (new \Domain\User\UsersTable($this->db))->whereEmail($email)->find();
        if (count($users) > 0) {
            $user = reset($users);
            if ($user->verify($password)) {
                return $user;
            }
        }
        return;
    }
}
