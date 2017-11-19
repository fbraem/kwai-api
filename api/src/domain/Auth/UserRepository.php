<?php
namespace Domain\Auth;

use Analogue\ORM\Repository;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class UserRepository extends Repository implements UserRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(\Domain\User\User::class);
    }

    public function getUserEntityByUserCredentials($email, $password, $grantType, ClientEntityInterface $client)
    {
        $user = $this->mapper->where('email', '=', $email)->first();
        if ($user) {
            if (password_verify($password, $user->password)) {
                $user->last_login = \Carbon\Carbon::now();
                $this->store($user);
                return $user;
            }
        }
        return;
    }
}
