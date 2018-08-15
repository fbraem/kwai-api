<?php
namespace Domain\Auth;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    public function getUserEntityByUserCredentials($email, $password, $grantType, ClientEntityInterface $client)
    {
        $user = \Domain\User\UsersTable::getTableFromRegistry()
            ->find()
            ->where(['email' => $email])
            ->first();
        if ($user) {
            if ($user->verify($password)) {
                return $user;
            }
        }
        return;
    }
}
